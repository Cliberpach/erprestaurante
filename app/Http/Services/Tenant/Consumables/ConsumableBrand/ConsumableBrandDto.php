<?php

namespace App\Http\Services\Tenant\Consumables\ConsumableBrand;

class ConsumableBrandDto
{
    public function getDtoStore(array $data)
    {
        $dto    =   [];
        $dto['name'] = mb_strtoupper($data['name'], 'UTF-8');
        return $data;
    }
}
