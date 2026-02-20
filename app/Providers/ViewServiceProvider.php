<?php

namespace App\Providers;

use App\Models\Tenant\Maintenance\Company\Company;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Spatie\Multitenancy\Models\Tenant;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(\App\Services\MenuService $menuService)
    {
        View::composer('layouts.template', function ($view) use ($menuService) {

            if (!auth()->check()) return;

            $isTenant   =   Tenant::checkCurrent();
            $base       =   $isTenant ? 'tenant' : 'landlord';
            $tenantId   =   $isTenant ? Tenant::current()->id : 'landlord';
            $user       =   auth()->user();
            $user->loadMissing('roles');

            $company = null;
            if ($isTenant) {
                $company = Cache::remember(
                    "company_tenant_{$tenantId}",
                    now()->addHours(12),
                    fn() => Company::findOrFail(1)
                );
            }

            $view->with([
                'modules'            => $menuService->getMenuForUser($user, $tenantId),
                'base'               => $base . '.',
                'lst_search_modules' => $this->getLstSearchModules($user, $tenantId),
                'tenant_id'          => $tenantId,
                'company'            => $company,
            ]);
        });
    }

    public function getLstSearchModules($user, $tenant_id)
    {
        $roleNames = $user->roles->pluck('name')->sort()->implode('_');

        return Cache::remember(
            "search_modules_{$tenant_id}_{$roleNames}",
            now()->addHours(6),
            function () use ($user) {

                $permissions = $user->getAllPermissions()->pluck('name');
                if ($permissions->isEmpty()) {
                    return [];
                }

                $children = DB::table('module_children as mc')
                    ->join('modules as m', 'm.id', '=', 'mc.module_id')
                    ->whereNotNull('mc.route_name')
                    ->whereIn('mc.route_name', $permissions)
                    ->select([
                        'mc.description as name',
                        'mc.route_name as url',
                        'm.description as category',
                        DB::raw("'fi fi-rr-file' as icon"),
                    ]);

                $grandChildren = DB::table('module_grand_children as mgc')
                    ->join('module_children as mc2', 'mc2.id', '=', 'mgc.module_child_id')
                    ->join('modules as m2', 'm2.id', '=', 'mc2.module_id')
                    ->whereNotNull('mgc.route_name')
                    ->whereIn('mgc.route_name', $permissions)
                    ->select([
                        'mgc.description as name',
                        'mgc.route_name as url',
                        'm2.description as category',
                        DB::raw("'fi fi-rr-file' as icon"),
                    ]);

                return $children
                    ->unionAll($grandChildren)
                    ->get();
            }
        );
    }
}
