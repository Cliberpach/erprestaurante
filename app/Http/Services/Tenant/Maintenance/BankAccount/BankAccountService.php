<?php

namespace App\Http\Services\Tenant\Maintenance\BankAccount;

use App\Greenter\Utils\Util;
use App\Http\Controllers\UtilController;
use App\Models\Bank;
use App\Models\Tenant\Maintenance\BankAccount\BankAccount;

class BankAccountService
{
    private BankAccountRepository $s_repository;
    private BankAccountDto $s_dto;

    public function __construct()
    {
        $this->s_repository    =   new BankAccountRepository();
        $this->s_dto           =   new BankAccountDto();
    }

    public function store(array $data): BankAccount
    {
        $dto    =   $this->s_dto->getDtoStore($data);
        $item   =   $this->s_repository->store($dto);

        $folder   =   'bank_accounts';
        UtilController::saveImg($data['qr'], $item->qr_name, $folder);

        return $item;
    }

    public function update(array $data, int $id): BankAccount
    {
        $dto            =   $this->s_dto->getDtoStore($data);

        $item_preview   =   $this->s_repository->find($id);
        $item           =   $this->s_repository->update($dto,$id);

        $folder         =   'bank_accounts';
        UtilController::saveImg($data['qr'], $item->qr_name, $folder);

        if($item_preview->qr_url){
            UtilController::deleteImg($item_preview->qr_url);
        }

        return $item;
    }

}
