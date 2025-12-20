<?php

namespace App\Http\Services\Tenant\Supply\TypeDish;

use App\Models\Tenant\Supply\Table\Table;
use App\Models\Tenant\Supply\TypeDish\TypeDish;

class TypeDishManagement
{
    private TypeDishService  $s_type_dish;

    public function __construct()
    {
        $this->s_type_dish    =   new TypeDishService();
    }

    public function store(array $data): TypeDish
    {
        return $this->s_type_dish->store($data);
    }

    public function update(array $data, int $id): TypeDish
    {
        return $this->s_type_dish->update($data, $id);
    }

    public function getOne(int $id): TypeDish
    {
        return $this->s_type_dish->getOne($id);
    }

    public function destroy(int $id): TypeDish
    {
        return $this->s_type_dish->destroy($id);
    }

    public function searchCashAvailable(array $data)
    {
        return $this->s_type_dish->searchCashAvailable($data);
    }
}
