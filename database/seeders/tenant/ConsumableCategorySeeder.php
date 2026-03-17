<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\Consumables\ConsumableCategory\ConsumableCategory;
use Illuminate\Database\Seeder;

class ConsumableCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $c           =   new ConsumableCategory();
        $c->name     =   'CATEGORIA';
        $c->status   =   'ANULADO';
        $c->save();

        $c           =   new ConsumableCategory();
        $c->name     =   'GENERICO';
        $c->status   =   'ACTIVO';
        $c->save();
    }
}
