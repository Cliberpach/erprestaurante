<?php

namespace App\Http\Services\Tenant\Supply\TypeDish;

use App\Models\Tenant\Supply\Table\Table;
use App\Models\Tenant\Supply\TypeDish\TypeDish;

class TypeDishService
{
    private TypeDishRepository $s_repository;
    private TypeDishDto $s_dto;

    public function __construct()
    {
        $this->s_repository =   new TypeDishRepository();
        $this->s_dto        =   new TypeDishDto();
    }

    public function store(array $data): TypeDish
    {
        $dto    =   $this->s_dto->getDtoStore($data);
        $item   =   $this->s_repository->insertTypeDish($dto);
        return $item;
    }

    public function update(array $data, int $id): TypeDish
    {
        $dto    =   $this->s_dto->getDtoStore($data);
        $cash   =   $this->s_repository->updateTypeDish($dto, $id);
        return $cash;
    }

    public function getOne(int $id): TypeDish
    {
        return $this->s_repository->findTypeDish($id);
    }

    public function destroy(int $id): TypeDish
    {
        return $this->s_repository->destroy($id);
    }

    public function searchCashAvailable(array $data)
    {
        $cashes = $this->s_repository->searchCashAvailable($data);
        return $cashes;
    }

    public function setStatus(int $id, string $status)
    {
        $this->s_repository->setStatus($id,$status);
    }
}
