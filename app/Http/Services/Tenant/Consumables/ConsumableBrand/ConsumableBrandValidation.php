<?php

namespace App\Http\Services\Tenant\Consumables\ConsumableBrand;

use Exception;

class ConsumableBrandValidation
{
    private ConsumableBrandRepository $s_repository;

    public function __construct(ConsumableBrandRepository $s_repository)
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
