<?php

namespace App\Jobs;

use App\Exceptions\SunatPermanentException;
use App\Exceptions\SunatTemporaryException;
use App\Http\Services\Tenant\Sale\Sale\SaleService;
use App\Models\Tenant\InvoiceDispatchLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Spatie\Multitenancy\Models\Tenant;

class SendInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 1;
    public int $timeout = 60;

    public function __construct(
        private readonly int $dispatchLogId,
        private readonly int $tenantId
    ) {}

    public function handle(): void
    {
        $tenant = Tenant::find($this->tenantId); // necesitas pasar tenantId al job
        if (!$tenant) {
            Log::warning("Tenant {$this->tenantId} no encontrado para log {$this->dispatchLogId}.");
            return;
        }
        $tenant->makeCurrent();


        $log = InvoiceDispatchLog::find($this->dispatchLogId);

        if (!$log) return;



        if ($log->isExpired()) {
            $log->update(['status' => InvoiceDispatchLog::STATUS_EXPIRED]);
            return;
        }

        if (!$log->canRetry()) return;
        if ($log->status === InvoiceDispatchLog::STATUS_PROCESSING) return;

        $log->update(['status' => InvoiceDispatchLog::STATUS_PROCESSING]);

        $tenant = Tenant::find($log->tenant_id);
        $tenant->makeCurrent();

        try {
            // ✅ Llama directo a tu servicio existente
            $saleService = app(SaleService::class);
            $sale = $saleService->sendSunat($log->invoiceable_id);

            // Evaluar resultado según sunat_status que ya guardas
            if ($sale->sunat_status === 'ACEPTADO') {
                $log->update([
                    'status'  => InvoiceDispatchLog::STATUS_ACCEPTED,
                    'sent_at' => now(),
                    'metadata' => [
                        'sunat_status'    => $sale->sunat_status,
                        'cdr_code'        => $sale->cdr_response_code,
                        'cdr_description' => $sale->cdr_response_description,
                    ],
                ]);
            } elseif ($sale->sunat_status === 'ENVIADO') {
                // Enviado pero sin CDR aún
                $log->update([
                    'status'  => InvoiceDispatchLog::STATUS_SENT,
                    'sent_at' => now(),
                    'metadata' => ['sunat_status' => $sale->sunat_status],
                ]);
            } elseif ($sale->sunat_status === 'RECHAZADO') {
                // RECHAZADO = error permanente, no reintentar
                throw new SunatPermanentException(
                    "RECHAZADO: [{$sale->response_error_code}] {$sale->response_error_message}"
                );
            } else {
                // PENDIENTE = error temporal, reintentar
                throw new SunatTemporaryException(
                    $sale->last_send_message ?? 'Sin respuesta de SUNAT'
                );
            }
        } catch (SunatPermanentException $e) {
            // No reintentar: doc inválido, duplicado, RUC inactivo
            $log->update([
                'status'     => InvoiceDispatchLog::STATUS_FAILED,
                'last_error' => [
                    'message' => $e->getMessage(),
                    'type'    => 'permanent',
                    'at'      => now(),
                ],
            ]);

            Log::error("❌ Error permanente SUNAT", [
                'log_id'  => $log->id,
                'sale_id' => $log->invoiceable_id,
                'error'   => $e->getMessage(),
            ]);
        } catch (SunatTemporaryException $e) {
            // Reintentar con backoff exponencial
            $log->markAsFailed($e->getMessage(), ['type' => 'temporary']);
            $log->refresh();

            if ($log->canRetry()) {
                self::dispatch($this->dispatchLogId)
                    ->onQueue('invoice-retries')
                    ->delay($log->calculateNextRetry());

                Log::warning("⚠️ Reintentando comprobante", [
                    'log_id'      => $log->id,
                    'sale_id'     => $log->invoiceable_id,
                    'attempts'    => $log->attempts,
                    'next_retry'  => $log->next_retry_at,
                ]);
            }
        } catch (\Throwable $e) {
            $log->markAsFailed($e->getMessage());

            Log::error("💥 Error inesperado", [
                'log_id'  => $log->id,
                'sale_id' => $log->invoiceable_id,
                'error'   => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);
        } finally {
            Tenant::forgetCurrent();
        }
    }
}
