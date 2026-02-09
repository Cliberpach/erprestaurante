<?php

namespace App\Http\Services\Tenant\Reservation;

use App\Http\Services\Tenant\Supply\Table\TableService;
use App\Models\Tenant\Orders\Order;
use App\Models\Tenant\Reservation\Reservation;

class ReservationService
{
    private ReservationDto $s_dto;
    private ReservationRepository $s_repository;
    private TableService $s_table;

    public function __construct()
    {
        $this->s_dto        =   new ReservationDto();
        $this->s_repository =   new ReservationRepository();
        $this->s_table      =   new TableService();
    }

    public function create(int $table_id) {}

    public function store(Order $order): Reservation
    {
        $dto            =   $this->s_dto->getDtoStore($order);
        $item           =   $this->s_repository->store($dto);
        $this->s_table->setStatus($item->table_id, 'OCUPADO');
        return $item;
    }

    public function setStatusByOrder(int $order_id, string $status)
    {
        $this->s_repository->setStatusByOrder($order_id, $status);
    }

    public function changeTable(int $order_id, int $table_selected, int $table_old)
    {
        $this->s_repository->changeTable($order_id, $table_selected);
        $this->s_table->setStatus($table_selected, 'OCUPADO');
        $this->s_table->setStatus($table_old, 'LIBRE');
    }

    public function deleteFromOrder(int $order_id, int $table_id)
    {
        $this->s_repository->deleteFromOrder($order_id);
        $this->s_table->setStatus($table_id, 'LIBRE');
    }
}
