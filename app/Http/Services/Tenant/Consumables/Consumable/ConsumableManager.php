<?php

namespace App\Http\Services\Tenant\Consumables\Consumable;

use App\Models\Tenant\Consumables\Consumable\Consumable;

class ConsumableManager
{
    protected ConsumableService $s_service;

    public function __construct()
    {
        $this->s_service   =   new ConsumableService();
    }

    public function getProduct(int $producto_id)
    {
        return $this->s_service->getProduct($producto_id);
    }

    public function store(array $data): Consumable
    {
        return $this->s_service->store($data);
    }

    public function update(int $id, array $data): Consumable
    {
        return $this->s_service->update($id, $data);
    }

    public function getList(array $filters)
    {
        return $this->s_service->getList($filters);
    }
}
