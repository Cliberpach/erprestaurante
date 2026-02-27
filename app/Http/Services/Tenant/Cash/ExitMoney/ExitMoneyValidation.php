<?php

namespace App\Http\Services\Tenant\Cash\ExitMoney;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExitMoneyValidation
{
    public function validationStore(array $data): array
    {   dd($data);
        $user               =   Auth::user();
        if (!$user->hasRole('CAJERO')) {
            throw new Exception("Solo cajeros pueden registrar egresos");
        }

        $petty_cash = DB::table('petty_cash_books')
            ->where('user_id', $user->id)
            ->where('status', 'ABIERTO')
            ->select(
                'id'
            )
            ->orderByDesc('id')
            ->first();

        if (!$petty_cash) {
            throw new Exception("NO FORMAS PARTE DE UNA CAJA ABIERTA");
        }

        $data['petty_cash_book'] =   $petty_cash;

        return $data;
    }
}
