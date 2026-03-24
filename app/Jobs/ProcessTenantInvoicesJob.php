<?php

namespace App\Jobs;

use App\Models\Tenant\InvoiceDispatchLog;
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
        $tenant = Tenant::find($this->tenantId);

        if (!$tenant) {
            Log::warning("Tenant {$this->tenantId} no encontrado.");
            return;
        }

        $tenant->makeCurrent();

        try {
            $this->dispatchPendingInvoices();
            $this->markExpiredInvoices();
        } finally {
            Tenant::forgetCurrent();
        }
    }

    private function dispatchPendingInvoices(): void
    {
        InvoiceDispatchLog::query()
            ->where('status', InvoiceDispatchLog::STATUS_PENDING)
            ->where(function ($q) {
                $q->whereNull('next_retry_at')
                    ->orWhere('next_retry_at', '<=', now());
            })
            ->where('expires_at', '>', now())
            ->chunkById(50, function ($logs) {
                foreach ($logs as $log) {
                    SendInvoiceJob::dispatch($log->id, $this->tenantId)
                        ->onQueue('invoices')
                        ->delay(now()->addSeconds(rand(1, 5)));
                }
            });
    }

    private function markExpiredInvoices(): void
    {
        InvoiceDispatchLog::query()
            ->whereNotIn('status', [
                InvoiceDispatchLog::STATUS_SENT,
                InvoiceDispatchLog::STATUS_ACCEPTED,
                InvoiceDispatchLog::STATUS_EXPIRED,
            ])
            ->where('expires_at', '<=', now())
            ->update(['status' => InvoiceDispatchLog::STATUS_EXPIRED]);
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
