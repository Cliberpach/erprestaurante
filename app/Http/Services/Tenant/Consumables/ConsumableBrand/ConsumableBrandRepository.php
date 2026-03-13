<?php

namespace App\Http\Services\Tenant\Consumables\ConsumableBrand;

use App\Models\Tenant\Consumables\ConsumableBrand\ConsumableBrand;

class ConsumableBrandRepository
{
    public function find(int $id)
    {
        return ConsumableBrand::findOrFail($id);
    }

    public function store(array $dto): ConsumableBrand
    {
        return ConsumableBrand::create($dto);
    }

    public function update(array $dto, int $id): ConsumableBrand
    {
        $instance   =   ConsumableBrand::findOrFail($id);
        $instance->update($dto);
        return $instance;
    }

    public function destroy(int $id): ConsumableBrand
    {
        $instance   =   ConsumableBrand::findOrFail($id);
        $instance->status   =   'ANULADO';
        $instance->update();
        return $instance;
    }
}
