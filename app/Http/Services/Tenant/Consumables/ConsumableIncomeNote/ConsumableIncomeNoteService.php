<?php

namespace App\Http\Services\Tenant\Consumables\ConsumableIncomeNote;

use App\Http\Services\Tenant\Consumables\ConsumableKardex\ConsumableKardexService;
use App\Models\Tenant\Consumables\Consumable\Consumable;
use App\Models\Tenant\Consumables\ConsumableIncomeNote\ConsumableIncomeNote;

class ConsumableIncomeNoteService
{
    private ConsumableIncomeNoteRepository $s_repository;
    private ConsumableIncomeNoteDto $s_dto;
    private ConsumableKardexService $s_kardex;

    public function __construct()
    {
        $this->s_repository =   new ConsumableIncomeNoteRepository();
        $this->s_dto        =   new ConsumableIncomeNoteDto();
        $this->s_kardex     =   new ConsumableKardexService();
    }

    public function storeFromConsumable(Consumable $instance): ConsumableIncomeNote
    {
        $dto_master =   $this->s_dto->getDtoMasterFromConsumable($instance);
        $note       =   $this->s_repository->store($dto_master);
        $dto_detail =   $this->s_dto->getDtoDetailFromConsumable($instance, $note->id);
        $this->s_repository->storeDetail($dto_detail);

        $this->s_kardex->storeFromIncomeNote($note, $dto_detail);
        return $note;
    }
}
