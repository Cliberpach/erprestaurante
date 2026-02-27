<?php

namespace App\Http\Services\Tenant\Cash\ExitMoney;

use App\Models\Tenant\Maintenance\CostCenter;
use App\Models\Tenant\PaymentMethod;
use Illuminate\Support\Facades\Auth;

class ExitMoneyDto
{
    public function getDtoStore(array $data): array
    {
        $dto    =   [];

        $payment_method =   PaymentMethod::findOrFail($data['payment_method_id']);
        $cost_center    =   CostCenter::findOrFail($data['cost_center']);
        $cash_book      =   $data['petty_cash_book'];

        $dto['proof_payment_id']    =   $data['proof_payment'];
        $dto['payment_method_id']   =   $payment_method->id;
        $dto['payment_method_name'] =   $payment_method->description;
        $dto['number']              =   $data['number'];
        $dto['date']                =   $data['date'];
        $dto['cost_center_id']      =   $cost_center->id;
        $dto['cost_center_name']    =   $cost_center->name;
        $dto['supplier_id']         =   $data['supplier_id'];
        $dto['user_id']             =   Auth::user()->id;
        $dto['petty_cash_book_id']  =   $cash_book->id;
        $dto['total']               =   0;

        return $dto;
    }

    public function getDtoDetail(array $data):array{
        $dto    =   [];
    }
}
