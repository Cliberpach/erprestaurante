<?php

namespace App\Http\Services\Tenant\Maintenance\User;

use App\Models\Tenant\User;

class UserRepository
{

    public function getMeserosLibres($data)
    {
        $petty_cash_book_id =   $data['id']??null;

        $servers =  User::from('users as u')
            ->leftJoin('petty_cash_servers as pcs', 'pcs.user_id', 'u.id')
            ->join('model_has_roles as mhr', 'mhr.model_id', 'u.id')
            ->join('roles as r', 'r.id', 'mhr.role_id')
            ->whereNull('pcs.user_id')
            ->where('r.name', 'MESERO')
            ->select(
                'u.id',
                'u.name as user_name',
                'r.name as role_name'
            );

        if($petty_cash_book_id){
            $servers->orWhere('pcs.petty_cash_book_id',$petty_cash_book_id);
        }

        return $servers->get();
    }
}
