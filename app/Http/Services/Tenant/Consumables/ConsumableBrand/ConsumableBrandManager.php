<?php

namespace App\Http\Services\Tenant\Consumables\ConsumableBrand;

use App\Models\Tenant\Consumables\ConsumableBrand\ConsumableBrand;

class ConsumableBrandManager
{
    protected ConsumableBrandService $s_service;

    public function __construct()
    {
        $this->s_service   =   new ConsumableBrandService();
    }

    public function store(array $data): ConsumableBrand
    {
        return $this->s_service->store($data);
    }

    public function update(array $data, int $id): ConsumableBrand
    {
        return $this->s_service->update($data, $id);
    }


    public function destroy(int $id):ConsumableBrand{
        return $this->s_service->destroy($id);
    }
}
