<?php

namespace App\Http\Services\Tenant\Consumables\Purchase;

use App\Http\Services\Tenant\Accounts\SupplierAccount\SupplierAccountService;
use App\Http\Services\Tenant\Consumables\ConsumableKardex\ConsumableKardexService;
use App\Http\Services\Tenant\Consumables\WarehouseConsumable\WarehouseConsumableService;
use App\Http\Services\Tenant\Inventory\Kardex\KardexService;
use App\Models\Tenant\Consumables\ConsumablePurchase\ConsumablePurchase;

class PurchaseService
{
    private PurchaseDto $s_dto;
    private PurchaseRepository $s_repository;
    private PurchaseValidation $s_validation;
    private WarehouseConsumableService $s_warehouse;
    private ConsumableKardexService $s_kardex;
    private SupplierAccountService $s_account;

    public function __construct()
    {
        $this->s_repository =   new PurchaseRepository();
        $this->s_dto        =   new PurchaseDto($this->s_repository);
        $this->s_validation =   new PurchaseValidation($this->s_repository);
        $this->s_warehouse  =   new WarehouseConsumableService();
        $this->s_kardex     =   new ConsumableKardexService();
        $this->s_account    =   new SupplierAccountService();
    }

    public function store(array $data): ConsumablePurchase
    {
        $data       =   $this->s_validation->validationStore($data);
        $dto        =   $this->s_dto->getDtoStore($data);
        $instance   =   $this->s_repository->store($dto);

        $dto_detail =   $this->s_dto->getDtoDetail($data['lst_purchase'], $instance);
        $this->s_repository->storeDetail($dto_detail);

        $lst_detail =   array_map(fn($item) => (object)$item, $dto_detail);
        $this->s_warehouse->increaseLstStock($lst_detail);

        $this->s_kardex->storeFromPurchaseConsumable($instance, $dto_detail);

        /*if ($item->payment_condition_id && $item->payment_condition_name !== 'CONTADO') {
            $this->s_account->store(['purchase_id' => $item->id]);
        }*/

        return $instance;
    }
}
