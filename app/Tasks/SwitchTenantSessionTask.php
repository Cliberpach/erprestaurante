<?php

namespace App\Tasks;

use Illuminate\Support\Str;
use Spatie\Multitenancy\Models\Tenant;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;
use Redis;

class SwitchTenantSessionTask implements SwitchTenantTask
{
    public function makeCurrent(Tenant $tenant): void
    {
        $this->setPrefix('tenant_' . $tenant->id);
    }

    public function forgetCurrent(): void
    {
        $this->setPrefix('landlord');
    }

    protected function setPrefix(string $context): void
    {
        $prefix = Str::slug(config('app.name'), '_') . '_' . $context . '_session_';

        // Actualiza el prefijo en la conexión session de Redis
        config(['database.redis.session.prefix' => $prefix]);

        // Fuerza reconexión para que aplique el nuevo prefijo
        app('redis')->connection('session')->client()->setOption(
            Redis::OPT_PREFIX,
            $prefix
        );
    }
}
