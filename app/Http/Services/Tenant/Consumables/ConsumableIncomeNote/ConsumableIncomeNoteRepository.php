<?php

namespace App\Http\Services\Tenant\Consumables\ConsumableIncomeNote;

use App\Models\Tenant\Consumables\ConsumableIncomeNote\ConsumableIncomeNote;
use App\Models\Tenant\Consumables\ConsumableIncomeNote\ConsumableIncomeNoteDetail;

class ConsumableIncomeNoteRepository
{
    public function store(array $dto): ConsumableIncomeNote
    {
        return ConsumableIncomeNote::create($dto);
    }

    public function storeDetail(array $dto)
    {
        ConsumableIncomeNoteDetail::insert($dto);
    }
}
