<?php

namespace App\Console\Commands;

use App\Models\Tenant\Sales\Sale\Sale;
use Illuminate\Console\Command;
use Spatie\Multitenancy\Models\Tenant;

class SyncAmountsSales extends Command
{
    protected $signature   = 'invoices:sync-amounts';
    protected $description = 'Sincroniza los montos de todas las ventas';

    public function handle(): void
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $tenant->makeCurrent();

            $this->info("📦 Procesando tenant: {$tenant->name}");

            //=========== BUSCAR TODAS SIN LOG ===========
            $sales = Sale::all();

            $this->info("   → {$sales->count()} ventas para sincronizar montos");

            foreach ($sales as $sale) {

                $factor                     =   (100 + $sale->igv_percentage) / 100;

                $sale->total_pay            =   $sale->total - $sale->discount;
                $sale->mto_imp_venta        =   $sale->total - $sale->discount;
                $sale->sub_total            =   $sale->mto_imp_venta;
                $sale->valor_venta          =   $sale->mto_imp_venta / $factor;
                $sale->total_impuestos      =   $sale->mto_imp_venta - $sale->valor_venta;
                $sale->mto_igv              =   $sale->total_impuestos;
                $sale->mto_oper_gravadas    =   $sale->valor_venta;
                $sale->saveQuietly();
            }

            Tenant::forgetCurrent();
        }

        $this->info("🎉 Sincronización completa en todos los tenants");
    }
}
