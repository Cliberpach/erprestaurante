<?php

namespace App\Http\Services\Tenant\Cash\PettyCashBook;

use App\Models\Tenant\Cash\PettyCash;
use Illuminate\Support\Facades\Auth;

class PettyCashBookDto
{
    public function getDtoStore(array $datos)
    {
        $petty_cash =   PettyCash::findOrFail($datos['cash_available_id']);

        $dto    =   [
            'petty_cash_id'     =>  $datos['cash_available_id'],
            'shift_id'          =>  $datos['shift'],
            'user_id'           =>  Auth::user()->id,
            'initial_amount'    =>  $datos['initial_amount'],
            'initial_date'      =>  now(),
            'petty_cash_name'   =>  $petty_cash->name
        ];

        return $dto;
    }

    public function getDtoCashServers(array $data,int $petty_cash_book_id){
        $dto = [];
        foreach ($data as $item) {
            $_item = [
                'petty_cash_book_id'   =>  $petty_cash_book_id,
                'user_id'              =>  $item
            ];
            $dto[]  =   $_item;
        }
        return $dto;
    }

     public function getDtoUpdate(array $datos,int $id)
    {

        $petty_cash =   PettyCash::findOrFail($datos['petty_cash_id']);

        $dto    =   [
            'petty_cash_id'     =>  $datos['petty_cash_id'],
            'shift_id'          =>  $datos['shift_edit'],
            'user_id'           =>  Auth::user()->id,
            'initial_amount'    =>  $datos['initial_amount_edit'],
            'initial_date'      =>  now(),
            'petty_cash_name'   =>  $petty_cash->name
        ];

        return $dto;
    }
}
