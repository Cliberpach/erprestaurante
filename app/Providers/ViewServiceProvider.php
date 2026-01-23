<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
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

    public function boot()
    {
        View::composer('*', function ($view) {

            $base = Tenant::checkCurrent() ? 'tenant' : 'landlord';

            if (!auth()->check()) {
                return;
            }

            $user = auth()->user();

            $menu = app(\App\Services\MenuService::class)->getMenuForUser($user);

            $view->with('modules', $menu);
            $view->with('base', $base . '.');
            $view->with('lst_search_modules', $this->getLstSearchModules($base, $user));
        });
    }

    public function getLstSearchModules(string $base, $user)
    {
        $roleNames = $user->roles->pluck('name')->sort()->implode('_');

        return Cache::remember(
            "search_modules_{$base}_{$roleNames}",
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
