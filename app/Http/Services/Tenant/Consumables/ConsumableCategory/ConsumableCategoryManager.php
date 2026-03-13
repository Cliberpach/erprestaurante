<?php

namespace App\Http\Services\Tenant\Consumables\ConsumableCategory;

use App\Models\Product;
use App\Models\Tenant\Consumables\ConsumableCategory\ConsumableCategory;

class ConsumableCategoryManager
{
    protected ConsumableCategoryService $s_service;

    public function __construct()
    {
        $this->s_service   =   new ConsumableCategoryService();
    }

    public function store(array $data): ConsumableCategory
    {
        return $this->s_service->store($data);
    }

    public function update(array $data, int $id): ConsumableCategory
    {
        return $this->s_service->update($data, $id);
    }


    public function destroy(int $id):ConsumableCategory{
        return $this->s_service->destroy($id);
    }
}
