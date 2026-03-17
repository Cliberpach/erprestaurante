<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\Consumables\ConsumableBrand\ConsumableBrand;
use Illuminate\Database\Seeder;

class ConsumableBrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $c           =   new ConsumableBrand();
        $c->name     =   'MARCA';
        $c->status   =   'ANULADO';
        $c->save();

        $c           =   new ConsumableBrand();
        $c->name     =   'GENERICO';
        $c->status   =   'ACTIVO';
        $c->save();
    }
}
