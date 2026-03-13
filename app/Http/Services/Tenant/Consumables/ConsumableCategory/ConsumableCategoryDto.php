<?php

namespace App\Http\Services\Tenant\Consumables\ConsumableCategory;

use App\Models\Landlord\GeneralTable\GeneralTableDetail;
use App\Models\Product;

class ConsumableCategoryDto
{
    public function getDtoStore(array $data)
    {
        $dto    =   [];
        $dto['name'] = mb_strtoupper($data['name'], 'UTF-8');
        return $data;
    }
}
