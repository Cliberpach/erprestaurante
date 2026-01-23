<?php

namespace App\Services;

use App\Models\Module;
use Illuminate\Support\Facades\Cache;
use Spatie\Multitenancy\Models\Tenant;

class MenuService
{
    public function getMenuForUser($user)
    {
        $tenantId = Tenant::current()?->id ?? 'landlord';
        $roleNames = $user->roles->pluck('name')->implode('_');

        return Cache::remember(
            "menu_{$tenantId}_{$roleNames}",
            now()->addHours(6),
            fn() => $this->buildMenu($user)
        );
    }

    private function buildMenu($user)
    {
        $base = Tenant::checkCurrent() ? 'tenant' : 'landlord';
        $permissions = $user->getAllPermissions()->pluck('name');

        return Module::where('show', $base)
            ->with([
                'children' => function ($q) use ( $base, $permissions) {
                    $q->where('show', $base)
                        ->whereIn('route_name', $permissions);
                },
                'children.grandchildren' => function ($q) use ( $base, $permissions) {
                    $q->where('show', $base)
                        ->whereIn('route_name', $permissions);
                },
            ])
            ->get()
            ->filter(fn($module) => $module->children->isNotEmpty())
            ->values();
    }
}
