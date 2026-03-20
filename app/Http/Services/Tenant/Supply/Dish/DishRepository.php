<?php

namespace App\Http\Services\Tenant\Supply\Dish;

use App\Models\Tenant\Supply\Dish\Dish;
use App\Models\Tenant\Supply\Dish\DishConsumable;

class DishRepository
{
    public function store(array $dto): Dish
    {
        return Dish::create($dto);
    }

    public function update(array $dto, int $id): Dish
    {
        $item    =   Dish::findOrFail($id);
        $item->update($dto);
        return $item;
    }

    public function find(int $id): Dish
    {
        return Dish::findOrFail($id);
    }

    public function destroy(int $id): Dish
    {
        $item           =   Dish::findOrFail($id);
        $item->status   =   'ANULADO';
        $item->save();
        return $item;
    }

    public function setStatus(int $id, string $status)
    {
        $item           =   Dish::findOrFail($id);
        $item->status   =   $status;
        $item->save();
    }

    public function storeDishConsumable(array $dto)
    {
        DishConsumable::insert($dto);
    }

    public function getDishConsumable(int $dish_id)
    {
        return DishConsumable::where('dish_id', $dish_id)->get();
    }

    public function destroyDishConsumable(int $dish_id)
    {
        DishConsumable::where('dish_id', $dish_id)->delete();
    }
}
