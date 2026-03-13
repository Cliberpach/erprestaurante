<?php

namespace App\Http\Services\Tenant\Consumables\Purchase;

use Exception;

class PurchaseValidation
{

    public function validationStore(array $data): array
    {
        $lstPurchaseDocument    =   json_decode($data['lstPurchaseDocument']);

        if (count($lstPurchaseDocument) === 0) {
            throw new Exception("El detalle de la compra insumos está vacío");
        }

        $collect_detail =   collect($lstPurchaseDocument);
        $quantity_total =   $collect_detail->sum('quantity');

        if ($quantity_total == 0) {
            throw new Exception("La cantidad de la compra debe ser mayor a 0");
        }

        $data['lst_purchase']   =   $lstPurchaseDocument;
        return $data;
    }
}
