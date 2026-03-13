<?php

namespace App\Http\Services\Tenant\Consumables\ConsumableKardex;

use App\Models\Tenant\Consumables\ConsumableIncomeNote\ConsumableIncomeNote;
use App\Models\Tenant\Consumables\ConsumablePurchase\ConsumablePurchase;

class ConsumableKardexService
{
    private ConsumableKardexDto $s_dto;
    private ConsumableKardexRepository $s_repository;

    public function  __construct()
    {
        $this->s_dto    =   new ConsumableKardexDto();
        $this->s_repository =   new ConsumableKardexRepository();
    }

    public function storeFromIncomeNote(ConsumableIncomeNote $note, array $detail)
    {
        $dto    =   $this->s_dto->getDtoStoreFromIncomeNote($note, $detail);
        $this->s_repository->store($dto);
    }

    public function storeFromPurchaseConsumable(ConsumablePurchase $purchase, array $detail)
    {
        $dto    =   $this->s_dto->getDtoStoreFromPurchase($purchase, $detail);
        $this->s_repository->store($dto);
    }
}
