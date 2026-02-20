<?php

namespace App\Http\Services\Tenant\Supply\Programming;

use App\Models\Company;
use App\Models\Tenant\Cash\PettyCashBook;
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

     public function getDtoUpdate(array $data)
    {
        $dto  = [];

        $dto['editor_user_id']       =   Auth::user()->id;
        $dto['editor_user_name']     =   Auth::user()->name;

        $lst_detail                  =   $data['lst_detail'];
        $dto['quantity_dishes']      =   count($lst_detail);
        $dto['total']                =   collect($lst_detail)->sum('quantity');

        return $dto;
    }

    public function getDtoDetail(array $datos, Programming $programming)
    {
        $lst_detail                  =   $datos['lst_detail'];

        $dto = [];
        foreach ($lst_detail as $item) {
            $_item  =   [
                'programming_id'    =>  $programming->id,
                'dish_id'           =>  $item['product_id'],
                'quantity'          =>  $item['quantity'],
                'stock'             =>  $item['quantity']
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

    public function getDtoAuto(PettyCashBook $petty_cash_book)
    {
        $dto['petty_cash_book_id']   =   $petty_cash_book->id;
        $dto['petty_cash_name']      =   $petty_cash_book->petty_cash_name;
        $dto['petty_cash_id']        =   $petty_cash_book->petty_cash_id;
        $dto['user_id']              =   $petty_cash_book->user_id;

        $count                       =   Dish::where('status', 'ACTIVO')->count();
        $dto['quantity_dishes']      =   $count;
        $dto['total']                =   200 * $count;

        return $dto;
    }

    public function getDtoLstAuto(Programming $programming)
    {
        $dishes =   Dish::with('typeDish')->where('status', 'ACTIVO')->get();
        $dto    =   [];
        foreach ($dishes as $dish) {

            $quantity = 200;
            $_item  =    [
                'programming_id' => $programming->id,
                'dish_id'        => $dish->id,

                'dish_name'      => $dish->name,
                'type_dish_name' => $dish->typeDish->name ?? '',

                'quantity'       => $quantity,
                'stock'          => $quantity,

                'purchase_price' => $dish->purchase_price,
                'sale_price'     => $dish->sale_price,

                'status'         => 'ACTIVO'
            ];

            $dto[]  =   $_item;
        }

        return $dto;
    }

    public function formatLstView($detail)
    {
        $dto    =   [];
        foreach ($detail as $item) {

            $_item  =    [

                'programming_id' => $item->programming_id,
                'product_id'     => $item->dish_id,

                'product_name'   => $item->dish_name,
                'type_dish_name' => $item->type_dish_name,

                'quantity'       => (int)$item->quantity,

                'purchase_price' => $item->purchase_price,
                'sale_price'     => $item->sale_price,

            ];

            $dto[]  =   $_item;
        }

        return $dto;
    }
}
