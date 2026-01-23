<?php

namespace Database\Seeders\tenant;

use Illuminate\Database\Seeder;
use App\Models\Tenant\Maintenance\Position;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $position           =   new Position();
        $position->name     =   'admin';
        $position->save();

        $position           =   new Position();
        $position->name     =   'MESERO';
        $position->save();

        $position           =   new Position();
        $position->name     =   'CAJERO';
        $position->save();

        $position           =   new Position();
        $position->name     =   'COCINERO';
        $position->save();

        $position           =   new Position();
        $position->name     =   'CONTADOR';
        $position->save();
    }
}
