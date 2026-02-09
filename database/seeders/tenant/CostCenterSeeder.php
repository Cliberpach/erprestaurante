<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\Maintenance\CostCenter;
use Illuminate\Database\Seeder;


class CostCenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cost               =   new CostCenter();
        $cost->name         =   'COMPRAS';
        $cost->save();

        $cost               =   new CostCenter();
        $cost->name         =   'DEVOLUCIÓN';
        $cost->save();
    }
}
