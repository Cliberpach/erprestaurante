<?php

namespace App\Http\Services\Tenant\Consumables\ConsumableKardex;

use App\Models\Tenant\Consumables\Consumable\Consumable;
use App\Models\Tenant\Consumables\ConsumableIncomeNote\ConsumableIncomeNote;
use App\Models\Tenant\Consumables\ConsumablePurchase\ConsumablePurchase;

class ConsumableKardexDto
{
    public function getDtoStoreFromIncomeNote(ConsumableIncomeNote $note, array $detail): array
    {
        $dto    =   [];
        foreach ($detail as  $item) {
            $consumable =   Consumable::findOrFail($item['consumable_id']);
            $_item  =   [];
            $_item['note_income_id']    =   $note->id;
            $_item['note_income_code']  =   'NIC-' . $note->id;
            $_item['type']              =   'ENTRADA';
            $_item['document_serie']    =   'NIC-' . $note->id;
            $_item['date']              =   $note->created_at;
            $_item['warehouse_id']      =   $note->warehouse_id;
            $_item['warehouse_name']    =   $note->warehouse_name;
            $_item['consumable_id']     =   $consumable->id;
            $_item['category_id']       =   $item['consumable_category_id'];
            $_item['brand_id']          =   $item['consumable_brand_id'];
            $_item['unit_name']         =   $item['unit_name'];
            $_item['unit_symbol']       =   $item['unit_symbol'];
            $_item['consumable_name']   =   $item['consumable_name'];
            $_item['category_name']     =   $item['consumable_category_name'];
            $_item['brand_name']        =   $item['consumable_brand_name'];
            $_item['quantity']          =   $item['quantity'];
            $_item['sale_price']        =   $consumable->sale_price;
            $_item['purchase_price']    =   $consumable->purchase_price;
            $_item['amount']            =   (float)$consumable->sale_price * (float)$item['quantity'];
            $_item['creator_user_id']   =   $note->creator_user_id;
            $_item['creator_user_name'] =   $note->creator_user_name;
            $_item['created_at']        =   now();
            $_item['updated_at']        =   now();
            $dto[]  =   $_item;
        }
        return $dto;
    }

    public function getDtoStoreFromPurchase(ConsumablePurchase $purchase, array $detail): array
    {
        $dto    =   [];
        foreach ($detail as  $item) {
            $consumable =   Consumable::findOrFail($item['consumable_id']);
            $_item  =   [];
            $_item['purchase_id']       =   $purchase->id;
            $_item['purchase_code']     =   'CC-' . $purchase->id;
            $_item['type']              =   'ENTRADA';
            $_item['document_serie']    =   'CC-' . str_pad($purchase->id, 8, '0', STR_PAD_LEFT);
            $_item['date']              =   $purchase->created_at;
            $_item['warehouse_id']      =   $purchase->warehouse_id;
            $_item['warehouse_name']    =   $purchase->warehouse_name;
            $_item['consumable_id']     =   $consumable->id;
            $_item['category_id']       =   $item['category_id'];
            $_item['brand_id']          =   $item['brand_id'];
            $_item['unit_name']         =   $item['unit_name'];
            $_item['unit_symbol']       =   $item['unit_symbol'];
            $_item['consumable_name']   =   $item['consumable_name'];
            $_item['category_name']     =   $item['category_name'];
            $_item['brand_name']        =   $item['brand_name'];
            $_item['quantity']          =   $item['quantity'];
            $_item['sale_price']        =   $consumable->sale_price;
            $_item['purchase_price']    =   $item['purchase_price'];
            $_item['amount']            =   $item['subtotal'];
            $_item['creator_user_id']   =   $purchase->creator_user_id;
            $_item['creator_user_name'] =   $purchase->creator_user_name;
            $_item['created_at']        =   now();
            $_item['updated_at']        =   now();
            $dto[]  =   $_item;
        }
        return $dto;
    }
}
