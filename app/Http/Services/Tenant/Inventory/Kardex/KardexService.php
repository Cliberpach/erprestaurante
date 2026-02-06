<?php

namespace App\Http\Services\Tenant\Inventory\Kardex;

use App\Models\Tenant\NoteIncome;
use App\Models\Tenant\NoteRelease;
use App\Models\Tenant\Orders\Order;
use App\Models\Tenant\PurchaseDocument;
use App\Models\Tenant\Sales\Sale\Sale;
use App\Models\Tenant\WorkShop\WorkOrder\WorkOrder;

class KardexService
{
    private KardexDto $s_dto;
    private KardexRepository $s_repository;

    public function __construct()
    {
        $this->s_dto        =   new KardexDto();
        $this->s_repository =   new KardexRepository();
    }

    public function storeFromNoteRelease(NoteRelease $note)
    {
        $dto    =   $this->s_dto->getDtoFromNoteRelease($note);
        $this->s_repository->insertKardex($dto);
    }

    public function storeFromNoteIncome(NoteIncome $note)
    {
        $dto    =   $this->s_dto->getDtoFromNoteIncome($note);
        $this->s_repository->insertKardex($dto);
    }

    public function storeFromSale(Sale $sale)
    {
        $dto    =   $this->s_dto->getDtoFromSale($sale);
        $this->s_repository->insertKardex($dto);
    }

    public function storeFromOrder(Order $instance)
    {
        $dto    =   $this->s_dto->getDtoFromOrder($instance);
        $this->s_repository->insertKardex($dto);
    }

    public function updateFromOrder(Order $instance)
    {
        $dto    =   $this->s_dto->getDtoFromOrder($instance);
        $this->s_repository->deleteKardexFromOrder($instance->id);
        if (!empty($dto)) {
            $this->s_repository->insertKardex($dto);
        }
    }

    public function storeFromPurchase(PurchaseDocument $purchase)
    {
        $dto    =   $this->s_dto->getDtoFromPurchase($purchase);
        $this->s_repository->insertKardex($dto);
    }

    public function storeFromWorkOrder(WorkOrder $work_order)
    {
        $dto    =   $this->s_dto->getDtoFromWorkOrder($work_order);
        $this->s_repository->insertKardex($dto);
    }
}
