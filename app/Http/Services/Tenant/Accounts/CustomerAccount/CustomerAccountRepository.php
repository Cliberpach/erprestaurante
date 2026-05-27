<?php

namespace App\Http\Services\Tenant\Accounts\CustomerAccount;

use App\Models\Tenant\Accounts\CustomerAccount;
use App\Models\Tenant\Accounts\CustomerAccountDetail;
use Illuminate\Support\Facades\DB;

class CustomerAccountRepository
{
    public function store(array $dto)
    {
        return CustomerAccount::create($dto);
    }

    public function insertPay($dto)
    {
        return CustomerAccountDetail::create($dto);
    }

    public function findCustomerAccount(int $id)
    {
        return CustomerAccount::findOrFail($id);
    }

    public function updateCustomerAccount(int $id, array $dto)
    {
        $customer_account = CustomerAccount::findOrFail($id);
        $customer_account->update($dto);
        return $customer_account;
    }

    public function getNexIdPay(int $customer_account_id)
    {
        return CustomerAccountDetail::where('customer_account_id', $customer_account_id)->count() + 1;
    }

    public function updateSalePayStatus(int $sale_id, string $pay_status): void
    {
        DB::table('sales')->where('id', $sale_id)->update(['pay_status' => $pay_status]);
    }
}
