<?php

namespace App\Http\Services\Tenant\Consumables\ConsumableCategory;

use App\Models\Tenant\Consumables\ConsumableCategory\ConsumableCategory;
use Illuminate\Support\Facades\DB;

class ConsumableCategoryRepository
{
    public function find(int $id)
    {
        return ConsumableCategory::findOrFail($id);
    }

    public function store(array $dto): ConsumableCategory
    {
        return ConsumableCategory::create($dto);
    }

    public function update(array $dto, int $id): ConsumableCategory
    {
        $instance   =   ConsumableCategory::findOrFail($id);
        $instance->update($dto);
        return $instance;
    }

    public function destroy(int $id): ConsumableCategory
    {
        $instance   =   ConsumableCategory::findOrFail($id);
        $instance->status   =   'ANULADO';
        $instance->update();
        return $instance;
    }
}
