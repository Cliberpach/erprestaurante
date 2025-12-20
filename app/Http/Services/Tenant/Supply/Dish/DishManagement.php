<?php

namespace App\Http\Services\Tenant\Supply\Dish;

use App\Models\Tenant\Supply\Dish\Dish;
use App\Models\Tenant\Supply\TypeDish\TypeDish;

class DishManagement
{
    private DishService  $s_manager;

    public function __construct()
    {
        $this->s_manager    =   new DishService();
    }

    public function store(array $data): Dish
    {
        return $this->s_manager->store($data);
    }

    public function update(array $data, int $id): Dish
    {
        return $this->s_manager->update($data, $id);
    }

    public function getOne(int $id): Dish
    {
        return $this->s_manager->getOne($id);
    }

    public function destroy(int $id): Dish
    {
        return $this->s_manager->destroy($id);
    }

}
