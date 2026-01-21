<?php

namespace App\Http\Services\Tenant\Maintenance\BankAccount;

use App\Models\Tenant\Maintenance\BankAccount\BankAccount;
use App\Models\Tenant\User;

class BankAccountRepository
{
    public function store(array $dto): BankAccount
    {
        return BankAccount::create($dto);
    }

    public function update(array $dto, int $id): BankAccount
    {
        $item   =   BankAccount::findOrFail($id);
        $item->update($dto);
        return $item;
    }

    public function find(int $id): BankAccount
    {
        return BankAccount::findOrFail($id);
    }
}
