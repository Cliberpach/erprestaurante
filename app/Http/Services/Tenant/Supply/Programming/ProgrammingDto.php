<?php

namespace App\Http\Services\Tenant\Supply\Programming;

use App\Models\Company;
use App\Models\Tenant\Supply\Dish\Dish;
use App\Models\Tenant\Supply\Programming\Programming;
use App\Models\Tenant\Supply\TypeDish\TypeDish;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class ProgrammingDto
{
    public function getDtoStore(array $datos)
    {
       $dto  = [];

       $dto['petty_cash_book_id']   =   $datos['petty_cash_book_id'];
       $dto['petty_cash_name']      =   $datos['petty_cash_name'];
       $dto['petty_cash_id']        =   $datos['petty_cash_id'];
       $dto['user_id']              =   Auth::user()->id;

       $lst_detail                  =   $datos['lst_detail'];
       $dto['quantity_dishes']      =   count($lst_detail);
       $dto['total']                =   collect($lst_detail)->sum('quantity');

        return $dto;
    }

     public function getDtoDetail(array $datos,Programming $programming)
    {
        $lst_detail                  =   $datos['lst_detail'];

        $dto = [];
        foreach ($lst_detail as $key => $item) {
            $_item  =   [
                'programming_id'    =>  $programming->id,
                'dish_id'           =>  $item['product_id'],
                'quantity'          =>  $item['quantity'],
            ];

            $dish       =   Dish::findOrFail($item['product_id']);
            $type_dish  =   TypeDish::findOrFail($dish->type_dish_id);

            $_item['type_dish_name']    =   $type_dish->name;
            $_item['dish_name']         =   $dish->name;
            $_item['purchase_price']    =   $dish->purchase_price;
            $_item['sale_price']        =   $dish->sale_price;

            $dto[]                      =   $_item;
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
