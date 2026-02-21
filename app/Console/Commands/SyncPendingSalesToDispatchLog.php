<?php

namespace App\Console\Commands;

use App\Models\Tenant\InvoiceDispatchLog;
use App\Models\Tenant\Sales\Sale\Sale;
use Illuminate\Console\Command;
use Spatie\Multitenancy\Models\Tenant;

class SyncPendingSalesToDispatchLog extends Command
{
    protected $signature   = 'invoices:sync-pending';
    protected $description = 'Sincroniza todas las ventas sin log hacia invoice_dispatch_logs';

    public function handle(): void
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $tenant->makeCurrent();

            $this->info("📦 Procesando tenant: {$tenant->name}");

            //=========== BUSCAR TODAS SIN LOG ===========
            $sales = Sale::query()
                ->whereIn('type_sale_code', ['01', '03'])
                ->where('status', 'ACTIVO')
                ->whereNotExists(function ($query) {
                    $query->from('invoice_dispatch_logs')
                        ->whereColumn('invoiceable_id', 'sales.id')
                        ->where('invoiceable_type', Sale::class);
                })
                ->get();

            $this->info("   → {$sales->count()} ventas sin log encontradas");

            $created  = 0;
            $expired  = 0;
            $accepted = 0;

            foreach ($sales as $sale) {

                //=========== CALCULAR EXPIRACIÓN SUNAT (3 días) ===========
                $expiresAt = $sale->created_at->addDays(3);

                //=========== DETERMINAR STATUS INICIAL ===========
                if ($sale->sunat_status === 'ACEPTADO') {
                    // Ya fue enviada exitosamente antes del sistema
                    $status  = InvoiceDispatchLog::STATUS_ACCEPTED;
                    $sentAt  = $sale->updated_at;
                    $accepted++;
                } elseif ($expiresAt->isPast()) {
                    // Nunca se envió y ya expiró
                    $status  = InvoiceDispatchLog::STATUS_EXPIRED;
                    $sentAt  = null;
                    $expired++;
                } else {
                    // Pendiente y aún dentro de los 3 días
                    $status  = InvoiceDispatchLog::STATUS_PENDING;
                    $sentAt  = null;
                    $created++;
                }

                //=========== CREAR LOG ===========
                InvoiceDispatchLog::create([
                    'tenant_id'        => $tenant->id,
                    'invoiceable_id'   => $sale->id,
                    'invoiceable_type' => Sale::class,
                    'status'           => $status,
                    'expires_at'       => $expiresAt,
                    'sent_at'          => $sentAt,
                    'metadata' => [
                        'sunat_status'    => $sale->sunat_status,
                        'response_cdrZip' => $sale->response_cdrZip ?? null,
                        'response_success' => $sale->response_success ?? null,

                        'response_error_code' => $sale->response_error_code ?? null,
                        'response_error_message' => $sale->response_error_message ?? null,

                        'cdr_response_id' => $sale->cdr_response_id ?? null,
                        'cdr_response_code' => $sale->cdr_response_code ?? null,
                        'cdr_response_description' => $sale->cdr_response_description ?? null,
                        'cdr_response_notes' => $sale->cdr_response_notes ?? null,
                        'cdr_response_reference' => $sale->cdr_response_reference ?? null,
                    ],
                ]);
            }

            //=========== RESUMEN POR TENANT ===========
            $this->info("   ✅ PENDIENTES creados : {$created}");
            $this->info("   ✅ ACEPTADOS históricos: {$accepted}");
            $this->info("   ⚠️  EXPIRADOS          : {$expired}");

            Tenant::forgetCurrent();
        }

        $this->info("🎉 Sincronización completa en todos los tenants");
    }
}
