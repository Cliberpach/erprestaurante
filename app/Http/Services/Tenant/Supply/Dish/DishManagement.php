<?php

namespace App\Http\Services\Tenant\Supply\Dish;

use App\Models\Tenant\Supply\Dish\Dish;

class DishManagement
{
    private DishService  $s_service;

    public function __construct()
    {
        $this->s_service    =   new DishService();
    }

    public function store(array $data): Dish
    {
        return $this->s_service->store($data);
    }

    public function update(array $data, int $id): Dish
    {
        return $this->s_service->update($data, $id);
    }

    public function getOne(int $id): Dish
    {
        return $this->s_service->getOne($id);
    }

    public function destroy(int $id): Dish
    {
        return $this->s_service->destroy($id);
    }

    public function searchDish(array $data)
    {
        return $this->s_service->searchDish($data);
    }

    public function formatLstSheet(int $dish_id): array
    {
        return $this->s_service->formatLstSheet($dish_id);
    }
}
