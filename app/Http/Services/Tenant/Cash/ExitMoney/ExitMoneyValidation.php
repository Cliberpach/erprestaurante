<?php

namespace App\Http\Services\Tenant\Cash\ExitMoney;

use App\Models\Tenant\Consumables\ConsumablePurchase\ConsumablePurchase;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExitMoneyValidation
{
    public function validationStore(array $data): array
    {
        $lst_details        =   json_decode($data['lstDetails']) ?? [];
        $this->validateExitDetails($lst_details);

        $user               =   Auth::user();
        if (!$user->hasRole('CAJERO')) {
            throw new Exception("Solo cajeros pueden registrar egresos");
        }

        $petty_cash = DB::table('petty_cash_books')
            ->where('user_id', $user->id)
            ->where('status', 'ABIERTO')
            ->select(
                'id'
            )
            ->orderByDesc('id')
            ->first();

        if (!$petty_cash) {
            throw new Exception("NO FORMAS PARTE DE UNA CAJA ABIERTA");
        }

        $total = 0;
        foreach ($lst_details as $item) {
            $total += (float) $item->total;
        }
        if ($total == 0) {
            throw new Exception("El total de los egresos debe ser mayor a 0");
        }

        $data['petty_cash_book']    =   $petty_cash;
        $data['lst_details']        =   $lst_details;
        $data['total']              =   $total;
        return $data;
    }

    public function validationUpdate(array $data): array
    {
        $exit_money =   $data['exit_money'];
        if ($exit_money->status == 0) {
            throw new Exception("El egreso está anulado, no puede editarse");
        }

        $user               =   Auth::user();
        if (!$user->hasRole('CAJERO')) {
            throw new Exception("Solo cajeros pueden editar egresos");
        }

        $lst_details        =   json_decode($data['lstDetails']) ?? [];
        $this->validateExitDetails($lst_details);


        $total = 0;
        foreach ($lst_details as $item) {
            $total += (float) $item->total;
        }
        if ($total == 0) {
            throw new Exception("El total de los egresos debe ser mayor a 0");
        }

        $data['lst_details']        =   $lst_details;
        $data['total']              =   $total;

        return $data;
    }

    public function validateExitDetails($lst_details)
    {
        if (!is_array($lst_details)) {
            throw new Exception("Formato de detalle inválido.");
        }

        if (count($lst_details) === 0) {
            throw new Exception("El detalle está vacío.");
        }

        $errors = [];

        foreach ($lst_details as $index => $item) {

            // Validar descripción
            if (
                !isset($item->description) ||
                trim($item->description) === ''
            ) {
                $errors[] = "La descripción está vacía en la fila " . ($index + 1);
            }

            // Validar total
            if (
                !isset($item->total) ||
                !is_numeric($item->total) ||
                $item->total <= 0
            ) {
                $errors[] = "El total debe ser mayor a 0 en la fila " . ($index + 1);
            }

            // 🔥 Limitar a 2 errores
            if (count($errors) >= 2) {
                break;
            }
        }

        if (!empty($errors)) {
            throw new Exception(implode("\n", $errors));
        }
    }

    public function validationDestroy()
    {
        $user               =   Auth::user();
        if (!$user->hasRole('CAJERO')) {
            throw new Exception("Solo cajeros pueden eliminar egresos");
        }
    }

    public function validationStoreFromPurchase(ConsumablePurchase $purchase): array
    {
        $user               =   Auth::user();
        if (!$user->hasRole('CAJERO')) {
            throw new Exception("Solo cajeros pueden registrar egresos");
        }

        $petty_cash = DB::table('petty_cash_books')
            ->where('user_id', $user->id)
            ->where('status', 'ABIERTO')
            ->select(
                'id'
            )
            ->orderByDesc('id')
            ->first();

        if (!$petty_cash) {
            throw new Exception("NO FORMAS PARTE DE UNA CAJA ABIERTA");
        }


        $data['petty_cash_book']    =   $petty_cash;
        $data['purchase']           =   $purchase;
        return $data;
    }
}
