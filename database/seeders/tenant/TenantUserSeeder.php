<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\Maintenance\Collaborator\Collaborator;
use App\Models\Tenant\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TenantUserSeeder extends Seeder
{
    public function run(): void
    {
        $this->create(
            collaborator: [
                'full_name'                  => 'ADMIN',
                'document_type_id'           => 1,
                'document_number'            => '77412431',
                'document_type_abbreviation' => 'DNI',
                'address'                    => 'AV HUSARES 123',
                'phone'                      => '999999999',
                'work_days'                  => 30,
                'rest_days'                  => 20,
                'monthly_salary'             => 9999,
                'daily_salary'               => 100,
                'position_id'                => 1,
            ],
            name: 'ADMIN',
            email: 'admin@gmail.com',
            role: 'admin'
        );

        $this->create(
            collaborator: [
                'full_name'                  => 'CAJERO 1',
                'document_type_id'           => 1,
                'document_number'            => '74571390',
                'document_type_abbreviation' => 'DNI',
                'address'                    => 'DIRECCION DEMO',
                'phone'                      => '994704968',
                'work_days'                  => 30,
                'rest_days'                  => 20,
                'monthly_salary'             => 1500,
                'daily_salary'               => 50,
                'position_id'                => 2,
            ],
            name: 'CAJERO 1',
            email: 'cajero@gmail.com',
            role: 'CAJERO'
        );

        $this->create(
            collaborator: [
                'full_name'                  => 'MESERO 1',
                'document_type_id'           => 1,
                'document_number'            => '71061619',
                'document_type_abbreviation' => 'DNI',
                'address'                    => 'DIRECCION DEMO',
                'phone'                      => '967429576',
                'work_days'                  => 30,
                'rest_days'                  => 20,
                'monthly_salary'             => 1500,
                'daily_salary'               => 50,
                'position_id'                => 3,
            ],
            name: 'MESERO 1',
            email: 'mesero1@gmail.com',
            role: 'MESERO'
        );
    }

    private function create(array $collaborator, string $name, string $email, string $role): void
    {
        $col = Collaborator::create(array_merge($collaborator, ['status' => 'ACTIVO']));

        $usr = User::create([
            'name'             => $name,
            'email'            => $email,
            'password'         => Hash::make('123456789'),
            'password_visible' => '123456789',
            'collaborator_id'  => $col->id,
            'status'           => 'ACTIVO',
        ]);

        $usr->assignRole(Role::where('name', $role)->firstOrFail());
    }
}
