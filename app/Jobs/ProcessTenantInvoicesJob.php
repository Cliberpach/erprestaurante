<?php

namespace App\Jobs;

use App\Models\Tenant\Sales\Sale\Sale;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Spatie\Multitenancy\Models\Tenant;

class ProcessTenantInvoicesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 1;
    public int $timeout = 300;

    public function __construct(
        private readonly int $tenantId
    ) {}

    public function handle(): void
    {
        Log::info("🟡 [JOB START] Iniciando ProcessTenantInvoicesJob", [
            'tenant_id' => $this->tenantId
        ]);
        $tenant = Tenant::find($this->tenantId);

        if (!$tenant) {
            Log::warning("Tenant {$this->tenantId} no encontrado.");
            return;
        }

        $tenant->makeCurrent();

        try {
            $this->dispatchPendingInvoices();
        } finally {
            Tenant::forgetCurrent();
        }
    }

    private function dispatchPendingInvoices(): void
    {
        Sale::query()
            ->whereNotIn('status', [
                Sale::STATUS_ACCEPTED,
                Sale::STATUS_OBSERVED,
            ])
            ->where('expires_at', '>=', now())
            ->whereNull('summary_id')
            ->whereIn('type_sale_code', ['03', '01'])
            ->where(function ($q) {
                $q->whereNull('processing_at')
                    ->orWhere('processing_at', '<', now()->subMinutes(10));
            })
            ->chunkById(50, function ($sales) {
                foreach ($sales as $sale) {

                    $updated = Sale::where('id', $sale->id)
                        ->where(function ($q) {
                            $q->whereNull('processing_at')
                                ->orWhere('processing_at', '<', now()->subMinutes(10));
                        })
                        ->update([
                            'processing_at' => now(),
                        ]);

                    if (!$updated) {
                        continue;
                    }

                    SendInvoiceJob::dispatch($sale->id, $this->tenantId)
                        ->onQueue('invoices')
                        ->delay(now()->addSeconds(rand(1, 5)));
                }
            });
    }


    /*
## Resumen visual del flujo:
```
01:00 AM todos los días
    └── Tenant 1 (ldrestaurante) → ProcessTenantInvoicesJob → SendInvoiceJob x N
    └── Tenant 2 (otrorestaurante) → ProcessTenantInvoicesJob → SendInvoiceJob x N

Cada hora (2am, 3am, 4am...)
    └── Reintenta los que fallaron con backoff exponencial
            15min → 1h → 3h → 8h → 24h (antes de los 3 días SUNAT)

*/
}
