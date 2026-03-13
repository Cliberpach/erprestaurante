<?php

namespace App\Http\Services\Tenant\Consumables\ConsumableCategory;

use Exception;

class ConsumableCategoryValidation
{
    private ConsumableCategoryRepository $s_repository;

    public function __construct(ConsumableCategoryRepository $s_repository)
    {
        $this->s_repository =   $s_repository;
    }

    public function validationUpdate(int $id)
    {
        $category   =   $this->s_repository->find($id);
        if ($category->status != 'ACTIVO') {
            throw new Exception("La categoría está anulada, no permitido editar");
        }
    }

    public function validationDestroy(int $id)
    {
        $category   =   $this->s_repository->find($id);
        if ($category->status != 'ACTIVO') {
            throw new Exception("La categoría está anulada, no permitido eliminar");
        }
    }
}
