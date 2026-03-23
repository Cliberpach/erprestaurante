<?php

namespace App\Http\Services\Tenant\Cash\ExitMoney;

use App\Models\Tenant\Cash\ExitMoney\ExitMoney;
use App\Models\Tenant\Consumables\ConsumablePurchase\ConsumablePurchase;

class ExitMoneyService
{
    private ExitMoneyDto  $s_dto;
    private ExitMoneyValidation $s_validation;
    private ExitMoneyRepository $s_repository;

    public function __construct()
    {
        $this->s_dto    =   new ExitMoneyDto();
        $this->s_validation =   new ExitMoneyValidation();
        $this->s_repository =   new ExitMoneyRepository();
    }

    public function store(array $data): ExitMoney
    {
        $data       =   $this->s_validation->validationStore($data);
        $dto        =   $this->s_dto->getDtoStore($data);
        $instance   =   $this->s_repository->store($dto);

        $dto_details    =   $this->s_dto->getDtoDetail($data['lst_details'], $instance);
        $this->s_repository->storeDetail($dto_details);
        return $instance;
    }

    public function storeFromCPurchase(ConsumablePurchase $purchase, array $lst_detail)
    {
        $data           =   $this->s_validation->validationStoreFromPurchase($purchase);
        $dto            =   $this->s_dto->getDtoStoreFromCPurchase($data);
        $instance       =   $this->s_repository->store($dto);

        $dto_details    =   $this->s_dto->getDtoDetailCPurchase($lst_detail, $instance);
        $this->s_repository->storeDetail($dto_details);
        return $instance;
    }

    public function update(array $data, int $id): ExitMoney
    {
        $exit_money         =   $this->s_repository->find($id);
        $data['exit_money'] =   $exit_money;
        $data               =   $this->s_validation->validationUpdate($data);

        $dto        =   $this->s_dto->getDtoUpdate($data);
        $instance   =   $this->s_repository->update($dto, $id);

        $this->s_repository->deleteDetail($id);
        $dto_details    =   $this->s_dto->getDtoDetail($data['lst_details'], $instance);
        $this->s_repository->storeDetail($dto_details);
        return $instance;
    }

    public function destroy(int $id): ExitMoney
    {
        $this->s_validation->validationDestroy();
        $exit_money =   $this->s_repository->delete($id);
        return $exit_money;
    }
}
