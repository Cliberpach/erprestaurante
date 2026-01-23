<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class TenantPermissionCloner
{
    public function clone(): void
    {
        DB::connection('tenant')->transaction(function () {

            /*
            |--------------------------------------------------------------------------
            | 1️⃣ Copiar PERMISSIONS
            |--------------------------------------------------------------------------
            */
            $landlordPermissions = Permission::on('landlord')->get();

            foreach ($landlordPermissions as $permission) {
                Permission::on('tenant')->firstOrCreate([
                    'name'       => $permission->name,
                    'guard_name' => $permission->guard_name,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 2️⃣ Copiar ROLES
            |--------------------------------------------------------------------------
            */
            $landlordRoles = Role::on('landlord')->get();

            foreach ($landlordRoles as $role) {
                Role::on('tenant')->firstOrCreate([
                    'name'       => $role->name,
                    'guard_name' => $role->guard_name,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 3️⃣ Copiar ROLE_HAS_PERMISSIONS
            |--------------------------------------------------------------------------
            */
            $landlordRolesWithPermissions =
                Role::on('landlord')->with('permissions')->get();

            foreach ($landlordRolesWithPermissions as $landlordRole) {

                $tenantRole = Role::on('tenant')
                    ->where('name', $landlordRole->name)
                    ->first();

                if (!$tenantRole) {
                    continue;
                }

                $permissionNames =
                    $landlordRole->permissions->pluck('name')->toArray();

                $tenantPermissions =
                    Permission::on('tenant')
                        ->whereIn('name', $permissionNames)
                        ->get();

                $tenantRole->syncPermissions($tenantPermissions);
            }

            /*
            |--------------------------------------------------------------------------
            | 4️⃣ Limpiar cache Spatie
            |--------------------------------------------------------------------------
            */
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        });
    }
}
