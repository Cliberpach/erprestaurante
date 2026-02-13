<?php

namespace App\Http\Services\Tenant\Supply\Table;

use Exception;

class TableValidation
{
    private TableRepository $s_repository;

    public function __construct(TableRepository $s_repository)
    {
        $this->s_repository =   $s_repository;
    }

    public function validationDestroy(int $id)
    {
        $table  =   $this->s_repository->findTable($id);

        if ($table->status === 'ANULADO') {
            throw new Exception("La mesa se encuentra eliminada");
        }

        $res   =   $this->s_repository->isNotFree($id);
        if ($res) {
            throw new Exception("LA MESA: " . $res->name . " tiene un pedido en curso: " . $res->code . ", eliminación no permitida.");
        }
    }
}
