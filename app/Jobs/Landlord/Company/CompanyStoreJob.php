<?php

namespace App\Jobs\Landlord\Company;

use App\Http\Services\Landlord\Maintenance\Company\CompanyManager;
use Throwable;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class CompanyStoreJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 900;
    public array $data;
    public int $tries = 1;

    /**
     * Create a new job instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $tenant = null;

        $start = microtime(true);

        try {

            Log::channel('tenant_store')
                ->info('INICIANDO CREACIÓN');

            DB::connection('landlord')->beginTransaction();

            $tenant = app(CompanyManager::class)
                ->store($this->data);

            DB::connection('landlord')->commit();

            $totalTime = microtime(true) - $start;

            Log::channel('tenant_store')
                ->info('TENANT CREADO CORRECTAMENTE', [
                    'tenant_id' => $tenant->id,
                    'database' => $tenant->database,
                    'execution_time_seconds' => round($totalTime, 2)
                ]);
        } catch (Throwable $th) {

            DB::connection('landlord')->rollBack();

            $totalTime = microtime(true) - $start;

            Log::channel('tenant_store')
                ->error('ERROR CREANDO TENANT', [
                    'message' => $th->getMessage(),
                    'file' => $th->getFile(),
                    'line' => $th->getLine(),
                    'execution_time_seconds' => round($totalTime, 2)
                ]);

            if ($tenant) {

                DB::connection('landlord')
                    ->statement("DROP DATABASE IF EXISTS `{$tenant->database}`");

                Log::channel('tenant_store')
                    ->warning('DATABASE ELIMINADA', [
                        'database' => $tenant->database
                    ]);
            }

            throw $th;
        }
    }
}
