<?php

namespace App\Http\Services\Tenant\Supply\Dish;

use App\Models\Company;
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

    public function getDtoUpdate(array $datos,int $id)
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
        }else{
            $dto['img_route']   =   null;
            $dto['img_name']    =   null;
        }

        return $dto;
    }
}
