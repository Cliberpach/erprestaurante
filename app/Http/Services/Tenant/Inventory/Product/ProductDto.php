<?php

namespace App\Http\Services\Tenant\Inventory\Product;

use App\Models\Landlord\GeneralTable\GeneralTableDetail;

class ProductDto
{
    public function getDtoStore(array $data)
    {
        $unit   =   GeneralTableDetail::findOrFail($data['unit_id']);

        $dto['name']            =   mb_strtoupper($data['name'], 'UTF-8');
        $data['unit_id']        =   $unit->id;
        $data['unit_symbol']    =   $unit->symbol;
        $data['unit_name']      =   $unit->name;

        return $data;
    }
}
