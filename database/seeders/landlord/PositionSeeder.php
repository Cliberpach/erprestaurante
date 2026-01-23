<?php

namespace Database\Seeders\Landlord;

use App\Models\Landlord\Maintenance\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createPosition('admin');
        $this->createPosition('MESERO');
        $this->createPosition('CAJERO');
        $this->createPosition('COCINERO');
        $this->createPosition('CONTADOR');
    }

    /* =========================
        Helper
    ========================== */

    private function createPosition(string $name): void
    {
        Position::firstOrCreate([
            'name' => $name,
        ]);
    }
}
