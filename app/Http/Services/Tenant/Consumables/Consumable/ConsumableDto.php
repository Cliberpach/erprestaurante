<?php

namespace App\Http\Services\Tenant\Consumables\Consumable;

use App\Models\Landlord\GeneralTable\GeneralTableDetail;

class ConsumableDto
{
    public function getDtoStore(array $data)
    {
        $unit   =   GeneralTableDetail::findOrFail($data['unit_id']);

        $data['name']           =   mb_strtoupper($data['name'], 'UTF-8');
        $data['description']    =   mb_strtoupper($data['description'], 'UTF-8');
        $data['unit_id']        =   $unit->id;
        $data['unit_symbol']    =   $unit->symbol;
        $data['unit_name']      =   $unit->name;

        return $data;
    }
}
