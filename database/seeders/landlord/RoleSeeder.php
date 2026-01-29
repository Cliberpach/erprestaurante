<?php

namespace Database\Seeders\landlord;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAdmin();
        $this->seedMesero();
        $this->seedCajero();
        $this->seedCocinero();
        $this->seedContador();
    }

    /* =========================
        Helpers
    ========================== */

    private function createRole(string $name, $permissions = null): void
    {
        $role = Role::firstOrCreate(['name' => $name]);

        if ($permissions instanceof \Illuminate\Support\Collection) {
            $role->syncPermissions($permissions);
        }
    }

    private function permissionsLike(array $prefixes)
    {
        return Permission::where(function ($q) use ($prefixes) {
            foreach ($prefixes as $prefix) {
                $q->orWhere('name', 'like', "{$prefix}%");
            }
        })->get();
    }

    /* =========================
        Roles
    ========================== */

    private function seedAdmin(): void
    {
        $this->createRole('admin', Permission::all());
    }

    private function seedMesero(): void
    {
        $permissions = $this->permissionsLike([
            'mostrador_mesero.',
        ]);

        $this->createRole('MESERO', $permissions);
    }

    private function seedCajero(): void
    {
        $permissions = $this->permissionsLike([
            'cajas.',
            'movimientos_caja.',
            'mostrador_cajero.'
        ]);

        $this->createRole('CAJERO', $permissions);
    }

    private function seedCocinero(): void
    {
        $this->createRole('COCINERO'); // sin permisos
    }

    private function seedContador(): void
    {
        $this->createRole('CONTADOR'); // sin permisos
    }
}
