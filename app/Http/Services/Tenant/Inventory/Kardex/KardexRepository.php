<?php

namespace App\Http\Services\Tenant\Inventory\Kardex;

use App\Models\Tenant\Inventory\Kardex\Kardex;

class KardexRepository
{
    public function insertKardex(array $dto)
    {
        Kardex::insert($dto);
    }

    public function deleteKardexFromOrder(int $order_id)
    {
        Kardex::where('order_id', $order_id)->delete();
    }
}
