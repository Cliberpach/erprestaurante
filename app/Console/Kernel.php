<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Spatie\Multitenancy\Models\Tenant;
use App\Jobs\ProcessTenantInvoicesJob;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        //=========== TAREA 1: ENVÍO NOCTURNO ===========
        // Se ejecuta todos los días a la 1AM automáticamente
        $schedule->call(function () {

            //=========== ITERAR TODOS LOS TENANTS ===========
            // Obtiene todos los restaurantes/empresas registradas
            // y despacha un job independiente por cada uno
            Tenant::all()->each(function ($tenant) {

                //=========== DESPACHAR JOB POR TENANT ===========
                // ProcessTenantInvoicesJob buscará todos los documentos
                // PENDIENTES de ese tenant y enviará cada uno a SUNAT
                // onQueue('invoices') = cola separada, no interfiere con usuarios
                ProcessTenantInvoicesJob::dispatch($tenant->id)
                    ->onQueue('invoices');
            });
        })
            //=========== CONFIGURACIÓN DE LA TAREA ===========
            ->dailyAt('01:00')          // Ejecutar cada día a la 1AM
            ->name('dispatch-nightly-invoices') // Nombre único para identificarla
            ->withoutOverlapping(60);   // Si tarda más de 60min, no ejecutar otra vez encima

        //=========== TAREA 2: REINTENTOS CADA HORA ===========
        // Por si algún documento falló (SUNAT caído, timeout, etc.)
        // cada hora vuelve a intentar enviar los que quedaron PENDIENTES
        $schedule->call(function () {

            //=========== MISMA LÓGICA, DIFERENTE COLA ===========
            // Usa 'invoice-retries' para separar reintentos del envío inicial
            // así los reintentos no bloquean los envíos nuevos
            Tenant::all()->each(function ($tenant) {
                ProcessTenantInvoicesJob::dispatch($tenant->id)
                    ->onQueue('invoice-retries'); // cola exclusiva de reintentos
            });
        })
            ->everySixHours()            // Cada 12 horas en punto (1am, 2am, 3am...)
            ->name('retry-failed-invoices') // Nombre único para identificarla
            ->withoutOverlapping(30);       // Si tarda más de 30min, no ejecutar encima

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
