<?php

namespace App\Http\Services\Tenant\Consumables\ConsumableIncomeNote;

use App\Models\Tenant\Consumables\Consumable\Consumable;
use App\Models\Tenant\Consumables\ConsumableBrand\ConsumableBrand;
use App\Models\Tenant\Consumables\ConsumableCategory\ConsumableCategory;

class ConsumableIncomeNoteDto
{
    public function getDtoMasterFromConsumable(Consumable $consumable): array
    {
        $dto    =   [];
        $dto['warehouse_id']    =   1;
        $dto['warehouse_name']  =   'CENTRAL';
        $dto['observation'] =   'NOTA DE INGRESO CREADA DESDE EL INSUMO: ' . $consumable->name;

        return $dto;
    }

    public function getDtoDetailFromConsumable(Consumable $consumable, int $note_id): array
    {
        $dto    =   [];
        $category   =   ConsumableCategory::findOrFail($consumable->category_id);
        $brand      =   ConsumableBrand::findOrFail($consumable->brand_id);

        $item   =   [];
        $item['consumable_income_note_id']   =   $note_id;
        $item['consumable_id']               =   $consumable->id;
        $item['consumable_brand_id']         =   $consumable->brand_id;
        $item['consumable_category_id']      =   $consumable->category_id;
        $item['warehouse_id']                =   1;
        $item['unit_id']                     =   $consumable->unit_id;
        $item['unit_symbol']                 =   $consumable->unit_symbol;
        $item['unit_name']                   =   $consumable->unit_name;
        $item['warehouse_name']              =   'CENTRAL';
        $item['consumable_name']             =   $consumable->name;
        $item['consumable_brand_name']       =   $brand->name;
        $item['consumable_category_name']    =   $category->name;
        $item['quantity']                    =   1;
        $item['created_at']                  =   now();
        $item['updated_at']                  =   now();

        $dto[]  =   $item;
        return $dto;
    }
}
