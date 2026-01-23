<?php

namespace App\Tasks;

use Illuminate\Support\Facades\Config;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;
use Spatie\Multitenancy\Models\Tenant;

class SwitchLandlordDatabaseTask implements SwitchTenantTask
{
    public function makeCurrent(Tenant $tenant): void
    {
        Config::set('database.default', 'landlord');
    }

    public function forgetCurrent(): void
    {
        Config::set('database.default', 'tenant');
    }
}
