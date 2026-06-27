<?php

namespace Database\Seeders\tenant;

use Illuminate\Database\Seeder;

use App\Models\Tenant\Configuration;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configuration              =   new Configuration();
        $configuration->description =   'Ambiente Facturación';
        $configuration->property    =   'BETA';
        $configuration->save();

        $configuration              =   new Configuration();
        $configuration->description =   'Solicitar contraseña para eliminar items en pedidos';
        $configuration->property    =   '1';
        $configuration->save();

        $configuration              =   new Configuration();
        $configuration->description =   'Habilitar descuentos en pedidos';
        $configuration->property    =   '1';
        $configuration->save();

        $configuration              =   new Configuration();
        $configuration->description =   'Contraseña acciones';
        $configuration->property    =   '123456789';
        $configuration->save();

        $configuration              =   new Configuration();
        $configuration->description =   'Máximo de mesas a fusionar';
        $configuration->property    =   '4';
        $configuration->save();
    }
}
