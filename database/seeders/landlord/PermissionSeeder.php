<?php

namespace Database\Seeders\Landlord;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use App\Models\ModuleChild;
use App\Models\ModuleGrandChild;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedFromChildren();
        $this->seedFromGrandChildren();
    }

    private function seedFromChildren(): void
    {
        ModuleChild::whereNotNull('route_name')
            ->pluck('route_name')
            ->each(fn($route) => $this->createPermission($route));
    }

    private function seedFromGrandChildren(): void
    {
        ModuleGrandChild::whereNotNull('route_name')
            ->pluck('route_name')
            ->each(fn($route) => $this->createPermission($route));
    }

    private function createPermission(string $route): void
    {
        Permission::firstOrCreate([
            'name' => $route,
            'guard_name' => 'web',
        ]);
    }
}
