<?php

namespace App\Http\Services\Tenant\Consumables\ConsumableCategory;

use App\Models\Company;
use App\Models\Product;
use App\Models\Tenant\Consumables\ConsumableCategory\ConsumableCategory;
use Illuminate\Support\Facades\DB;

class ConsumableCategoryService
{
    private ConsumableCategoryRepository $s_repository;
    private ConsumableCategoryDto $s_dto;
    private ConsumableCategoryValidation $s_validation;

    public function __construct()
    {
        $this->s_dto    =   new ConsumableCategoryDto();
        $this->s_repository =   new ConsumableCategoryRepository();
        $this->s_validation =   new ConsumableCategoryValidation($this->s_repository);
    }

    public function store(array $data): ConsumableCategory
    {
        $dto    =   $this->s_dto->getDtoStore($data);
        $instance   =   $this->s_repository->store($dto);
        return $instance;
    }

    public function update(array $data, int $id): ConsumableCategory
    {
        $this->s_validation->validationUpdate($id);
        $dto    =   $this->s_dto->getDtoStore($data);
        $instance   =   $this->s_repository->update($dto, $id);
        return $instance;
    }

    public function destroy(int $id): ConsumableCategory
    {
        $this->s_validation->validationDestroy($id);
        $instance   =   $this->s_repository->destroy($id);
        return $instance;
    }
}
