<?php

namespace App\Http\Services\Tenant\Maintenance\BankAccount;

use App\Models\Bank;
use App\Models\Tenant\Maintenance\BankAccount\BankAccount;

class BankAccountManager
{

    protected BankAccountService $s_service;

    public function __construct() {
        $this->s_service    =   new BankAccountService();
    }

    public function store(array $data):BankAccount{
        return $this->s_service->store($data);
    }

    public function update(array $data,int $id):BankAccount{
        return $this->s_service->update($data,$id);
    }

}
