<?php

namespace App\Http\Services\Tenant\Supply\TypeDish;

class TypeDishDto
{
    public function getDtoStore(array $datos)
    {
        $dto    =   [
            'name' => mb_strtoupper($datos['name'], 'UTF-8'),
        ];

        return $dto;
    }
}
