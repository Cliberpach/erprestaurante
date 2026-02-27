<?php

namespace App\Http\Services\Tenant\Supply\Programming;

use App\Http\Services\Tenant\Cash\PettyCashBook\PettyCashBookService;
use App\Models\Tenant\Cash\PettyCashBook;
use Exception;

class ProgrammingValidation
{
    private PettyCashBookService $pcb_service;
    private ProgrammingRepository $s_repository;

    public function __construct(ProgrammingRepository $s_repository)
    {
        $this->pcb_service  =   new PettyCashBookService();
        $this->s_repository =   $s_repository;
    }

    public function validationStore(array $data)
    {
        $petty_cash_id      =   $data['cash_available_id'];
        $petty_cash_book    =   $this->pcb_service->getCashBookCash($petty_cash_id);

        if (!$petty_cash_book) {
            throw new Exception("Debes seleccionar una caja abierta.");
        }

        $programming    =   $this->pcb_service->hasProgrammingActive($petty_cash_book->petty_cash_book_id);
        if ($programming === false) {
            throw new Exception("EL MOVIMIENTO DE CAJA: CM-" . $petty_cash_book->petty_cash_book_id . ", TIENE MÁS DE UNA PROGRAMACIÓN ACTIVA");
        }
        if ($programming) {
            throw new Exception("EL MOVIMIENTO DE CAJA: CM-" . $petty_cash_book->petty_cash_book_id . " YA TIENE UNA PROGRAMACIÓN ACTIVA");
        }

        $data['petty_cash_book_id']   =   $petty_cash_book->petty_cash_book_id;
        $data['petty_cash_name']      =   $petty_cash_book->petty_cash_name;
        $data['petty_cash_id']        =   $petty_cash_book->petty_cash_id;

        $lst_detail    =   json_decode($data['lst_detail'], true);
        if (empty($lst_detail) || count($lst_detail) == 0) {
            throw new Exception("Debe agregar al menos un detalle a la programación.");
        }

        $data['lst_detail']    =   $lst_detail;

        return $data;
    }

    public function validationUpdate(array $data, int $id)
    {
        $programming        =   $data['programming'];

        if ($programming->status != 'ACTIVO') {
            throw new Exception("La programación se encuentra con estado: " . $programming->status);
        }

        $petty_cash_book    =   $this->s_repository->belongsPettyCashBookActive($id);
        if ($petty_cash_book === false) {
            throw new Exception("La programación pertenece a más de una caja abierta");
        }
        if ($petty_cash_book === null) {
            throw new Exception("La programación no pertenece a ninguna caja abierta");
        }



        $lst_detail         =   json_decode($data['lst_detail'], true);
        if (empty($lst_detail) || count($lst_detail) == 0) {
            throw new Exception("Debe agregar al menos un detalle a la programación.");
        }
        $data['lst_detail']    =   $lst_detail;

        return $data;
    }

    public function validationAuto(PettyCashBook $petty_cash_book)
    {
        $programming    =   $this->pcb_service->hasProgrammingActive($petty_cash_book->id);
        if ($programming === false) {
            throw new Exception("EL MOVIMIENTO DE CAJA: CM-" . $petty_cash_book->id . ", TIENE MÁS DE UNA PROGRAMACIÓN ACTIVA");
        }
        if ($programming) {
            throw new Exception("EL MOVIMIENTO DE CAJA: CM-" . $petty_cash_book->id . " YA TIENE UNA PROGRAMACIÓN ACTIVA");
        }
    }
}
