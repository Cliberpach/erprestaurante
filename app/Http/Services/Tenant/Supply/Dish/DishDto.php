<?php

namespace App\Http\Services\Tenant\Supply\Dish;

use App\Models\Company;
use App\Models\Tenant\Consumables\Consumable\Consumable;
use App\Models\Tenant\Supply\Dish\Dish;
use Illuminate\Http\UploadedFile;

class DishDto
{
    public function getDtoStore(array $datos)
    {
        $dto    =   [
            'name'              =>  mb_strtoupper($datos['name'], 'UTF-8'),
            'type_dish_id'      =>  $datos['type_dish_id'],
            'purchase_price'    =>  $datos['purchase_price'],
            'sale_price'        =>  $datos['sale_price'],
        ];

        if (isset($datos['img']) && $datos['img'] instanceof UploadedFile) {

            $carpet_company =   Company::findOrFail(1)->files_route;
            $file           =   $datos['img'];
            $extension      =   $file->getClientOriginalExtension();
            $count          =   Dish::count() + 1;
            $filename       =   'dish_' . $count . '.' . $extension;

            $dto['img_route']   =   "storage/{$carpet_company}/dishes/images/{$filename}";
            $dto['img_name']    =   $filename;
        }

        return $dto;
    }

    public function getDtoDishConsumable(array $lst_sheet, Dish $dish): array
    {
        $dto = [];

        foreach ($lst_sheet as $item) {
            $_item  =   [
                'consumable_id' =>  $item->id,
                'dish_id'       =>  $dish->id,
                'quantity'      =>  (float)$item->quantity,
                'created_at'    =>  now(),
                'updated_at'    =>  now()
            ];
            $dto[]  =   $_item;
        }

        return $dto;
    }

    public function getDtoUpdate(array $datos, int $id)
    {

        $dto    =   [
            'name'              =>  mb_strtoupper($datos['name'], 'UTF-8'),
            'type_dish_id'      =>  $datos['type_dish_id'],
            'purchase_price'    =>  $datos['purchase_price'],
            'sale_price'        =>  $datos['sale_price'],
        ];

        if (isset($datos['img']) && $datos['img'] instanceof UploadedFile) {

            $carpet_company =   Company::findOrFail(1)->files_route;
            $file           =   $datos['img'];
            $extension      =   $file->getClientOriginalExtension();
            $count          =   $id;
            $filename       =   'dish_' . $count . '.' . $extension;

            $dto['img_route']   =   "storage/{$carpet_company}/dishes/images/{$filename}";
            $dto['img_name']    =   $filename;
        } else {
            $dto['img_route']   =   null;
            $dto['img_name']    =   null;
        }

        return $dto;
    }

    public function formatDishConsumer($lst_items): array
    {
        $lst    =   [];
        foreach ($lst_items as $item) {
            $consumable =   Consumable::findOrFail($item->consumable_id);
            $_item  =   (object)[
                'id'        =>  $item->consumable_id,
                'name'      =>  $consumable->name,
                'unit_name' =>  $consumable->unit_name,
                'quantity'  =>  $item->quantity
            ];
            $lst[]  =   $_item;
        }
        return $lst;
    }
}
