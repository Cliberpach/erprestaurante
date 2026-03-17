<?php

namespace App\Http\Services\Tenant\Cash\ExitMoney;

use App\Models\Tenant\Cash\ExitMoney\ExitMoney;
use App\Models\Tenant\Consumables\ConsumablePurchase\ConsumablePurchase;
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
        $dto['total']               =   $data['total'];
        $dto['discount_cash']       =   isset($data['discount_cash']) ? true : false;

        return $dto;
    }

    public function getDtoDetail(array $lst_details, ExitMoney $exit_money): array
    {
        $now = now();

        $dto = [];

        foreach ($lst_details as $item) {

            $dto[] = [
                'exit_money_id' => $exit_money->id,
                'description'   => strtoupper(trim($item->description)),
                'total'         => (float) $item->total,
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
        }

        return $dto;
    }

    public function getDtoUpdate(array $data)
    {
        $dto    =   [];

        $payment_method =   PaymentMethod::findOrFail($data['payment_method_id']);
        $cost_center    =   CostCenter::findOrFail($data['cost_center']);

        $dto['proof_payment_id']    =   $data['proof_payment'];
        $dto['payment_method_id']   =   $payment_method->id;
        $dto['payment_method_name'] =   $payment_method->description;
        $dto['number']              =   $data['number'];
        $dto['date']                =   $data['date'];
        $dto['cost_center_id']      =   $cost_center->id;
        $dto['cost_center_name']    =   $cost_center->name;
        $dto['supplier_id']         =   $data['supplier_id'];
        $dto['user_id']             =   Auth::user()->id;
        $dto['total']               =   $data['total'];
        $dto['discount_cash']       =   isset($data['discount_cash']) ? true : false;

        return $dto;
    }

    public function getDtoStoreFromCPurchase(array $data): array
    {
        $dto    =   [];

        $payment_method =   PaymentMethod::findOrFail(1);
        $cash_book      =   $data['petty_cash_book'];
        $purchase       =   $data['purchase'];

        $dto['proof_payment_id']    =   5;
        $dto['payment_method_id']   =   $payment_method->id;
        $dto['payment_method_name'] =   $payment_method->description;
        $dto['number']              =   $purchase->serie . '-' . $purchase->correlative;
        $dto['date']                =   $purchase->created_at;
        $dto['cost_center_id']      =   $purchase->cost_center_id;
        $dto['cost_center_name']    =   $purchase->cost_center_name;
        $dto['supplier_id']         =   1;
        $dto['user_id']             =   $purchase->creator_user_id;
        $dto['petty_cash_book_id']  =   $cash_book->id;
        $dto['total']               =   $purchase->total;
        $dto['discount_cash']       =   true;
        $dto['consumable_purchase_id']         =   $purchase->id;

        return $dto;
    }


    public function getDtoDetailCPurchase(array $lst_details, ExitMoney $exit_money): array
    {
        $now = now();

        $dto = [];

        foreach ($lst_details as $item) {

            $dto[] = [
                'exit_money_id' => $exit_money->id,
                'description'   => strtoupper(trim($item->consumable_name)),
                'total'         => (float) $item->subtotal,
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
        }

        return $dto;
    }
}
