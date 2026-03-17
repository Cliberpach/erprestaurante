<?php

namespace App\Http\Services\Tenant\Consumables\Purchase;

use App\Models\Tenant\Consumables\ConsumablePurchase\ConsumablePurchase;
use App\Models\Tenant\Consumables\ConsumablePurchase\ConsumablePurchaseDetail;
use App\Models\Tenant\PurchaseDocumentDetail;

class PurchaseRepository
{
    public function store(array $dto): ConsumablePurchase
    {
        return ConsumablePurchase::create($dto);
    }

    public function storeDetail(array $dto)
    {
        ConsumablePurchaseDetail::insert($dto);
    }

    public function find(int $id): ConsumablePurchase
    {
        return ConsumablePurchase::findOrFail($id);
    }

    public function getDetails(int $id)
    {
        return ConsumablePurchaseDetail::where('purchase_id', $id)->get();
    }
}
