<?php

namespace App\Http\Services\Tenant\Consumables\ConsumableKardex;

use App\Models\Tenant\Consumables\ConsumableKardex\ConsumableKardex;

class ConsumableKardexRepository
{
    public function store(array $dto)
    {
        ConsumableKardex::insert($dto);
    }
}
