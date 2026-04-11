<?php

namespace App\Jobs;

use App\Http\Services\Tenant\Sale\Sale\SaleService;
use App\Models\Tenant\Sales\Sale\Sale;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Spatie\Multitenancy\Models\Tenant;
use Throwable;

class SendInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 10;
    public int $timeout = 60;

    public function __construct(
        private readonly int $dispatchLogId,
        private readonly int $tenantId
    ) {}

    public function backoff(): int
    {
        return 1800; // 30 minutos
    }

    public function handle(): void
    {
        $tenant = Tenant::find($this->tenantId);
        if (!$tenant) {
            Log::warning("Tenant {$this->tenantId} no encontrado para log {$this->dispatchLogId}.");
            return;
        }
        $tenant->makeCurrent();

        $log_name   =   'send_invoice';

        try {
            $sale = Sale::find($this->dispatchLogId);
            Log::warning('Sale encontrada', [
                'sale' => $sale?->toArray()
            ]);

            if (!$sale) return;


            Log::warning('PROCESO');

            if ($sale->type_sale_code == '01') {
                $log_name   =   'send_facturas';
            }
            if ($sale->type_sale_code == '03') {
                $log_name   =   'send_boletas';
            }

            Log::warning('LOG NAME', [
                'log_name' => $log_name
            ]);

            $saleService = app(SaleService::class);
            $sale_send = $saleService->sendSunat($sale->id);

            Log::channel($log_name)->info("Envío completado", [
                'sale_id' => $sale_send->id ?? $sale->id ?? null,

                'response_cdrZip'              => $sale_send->response_cdrZip ?? null,
                'response_success'             => $sale_send->response_success ?? null,
                'response_error_code'          => $sale_send->response_error_code ?? null,
                'response_error_message'       => $sale_send->response_error_message ?? null,

                'cdr_response_id'              => $sale_send->cdr_response_id ?? null,
                'cdr_response_code'            => $sale_send->cdr_response_code ?? null,
                'cdr_response_description'     => $sale_send->cdr_response_description ?? null,
                'cdr_response_notes'           => $sale_send->cdr_response_notes ?? null,
                'cdr_response_reference'       => $sale_send->cdr_response_reference ?? null,
            ]);

            if (!in_array($sale_send->sunat_status, [
                Sale::STATUS_ACCEPTED,
                Sale::STATUS_OBSERVED,
            ])) {
                throw new Exception("Error en el envío, reintentando...");
            }
        } catch (Throwable $e) {
            Log::channel($log_name)->error("💥 Error inesperado", [
                'sale_id' => $sale->id ?? null,
                'error'   => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);
            throw $e;
        } finally {
            if ($sale) {
                $sale->update([
                    'processing_at' => null
                ]);
            }
            Tenant::forgetCurrent();
        }
    }
}
