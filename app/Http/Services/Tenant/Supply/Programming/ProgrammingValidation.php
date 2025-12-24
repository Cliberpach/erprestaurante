<?php

namespace App\Http\Services\Tenant\Supply\Programming;

use App\Http\Services\Tenant\Cash\PettyCashBook\PettyCashBookService;
use Exception;
use Illuminate\Support\Facades\Auth;

class ProgrammingValidation
{
    private PettyCashBookService $pcb_service;

    public function __construct()
    {
        $this->pcb_service   =   new PettyCashBookService();
    }

    public function validationStore(array $datos)
    {

        $petty_cash_id      =   $datos['cash_available_id'];
        $petty_cash_book    =   $this->pcb_service->getCashBookUser(Auth::user()->id);

        if (!$petty_cash_book) {
            throw new Exception("Debes pertenecer a una caja abierta.");
        }
        if ($petty_cash_book->petty_cash_id != $petty_cash_id) {
            throw new Exception("La caja seleccionada no corresponde a tu caja abierta.");
        }

        $programming    =   $this->pcb_service->hasProgrammingActive($petty_cash_book->petty_cash_book_id);
        if ($programming === false) {
            throw new Exception("EL MOVIMIENTO DE CAJA: CM-" . $petty_cash_book->petty_cash_book_id . ", TIENE MÁS DE UNA PROGRAMACIÓN ACTIVA");
        }
        if ($programming) {
            throw new Exception("EL MOVIMIENTO DE CAJA: CM-" . $petty_cash_book->petty_cash_book_id . " YA TIENE UNA PROGRAMACIÓN ACTIVA");
        }

        $datos['petty_cash_book_id']   =   $petty_cash_book->petty_cash_book_id;
        $datos['petty_cash_name']      =   $petty_cash_book->petty_cash_name;
        $datos['petty_cash_id']        =   $petty_cash_book->petty_cash_id;

        $lst_detail    =   json_decode($datos['lst_detail'], true);
        if (empty($lst_detail) || count($lst_detail) == 0) {
            throw new Exception("Debe agregar al menos un detalle a la programación.");
        }

        $datos['lst_detail']    =   $lst_detail;

        return $datos;
    }
}
