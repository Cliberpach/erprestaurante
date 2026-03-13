<?php

namespace App\Http\Services\Tenant\Consumables\ConsumableBrand;

use App\Models\Tenant\Consumables\ConsumableBrand\ConsumableBrand;

class ConsumableBrandService
{
    private ConsumableBrandRepository $s_repository;
    private ConsumableBrandDto $s_dto;
    private ConsumableBrandValidation $s_validation;

    public function __construct()
    {
        $this->s_dto    =   new ConsumableBrandDto();
        $this->s_repository =   new ConsumableBrandRepository();
        $this->s_validation =   new ConsumableBrandValidation($this->s_repository);
    }

    public function store(array $data): ConsumableBrand
    {
        $dto    =   $this->s_dto->getDtoStore($data);
        $instance   =   $this->s_repository->store($dto);
        return $instance;
    }

    public function update(array $data, int $id): ConsumableBrand
    {
        $this->s_validation->validationUpdate($id);
        $dto    =   $this->s_dto->getDtoStore($data);
        $instance   =   $this->s_repository->update($dto, $id);
        return $instance;
    }

    public function destroy(int $id): ConsumableBrand
    {
        $this->s_validation->validationDestroy($id);
        $instance   =   $this->s_repository->destroy($id);
        return $instance;
    }
}
