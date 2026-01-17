<?php

namespace App\Http\Services\Tenant\Orders;

use App\Http\Services\Tenant\Reservation\ReservationService;
use App\Models\Tenant\Orders\Order;
use Illuminate\Contracts\View\View;

class OrderService
{
    private OrderValidation $s_validation;
    private OrderDto $s_dto;
    private OrderRepository $s_repository;
    private ReservationService $s_reservation;

    public function __construct()
    {
        $this->s_validation     =   new OrderValidation();
        $this->s_dto            =   new OrderDto();
        $this->s_repository     =   new OrderRepository();
        $this->s_reservation    =   new ReservationService();
    }

    public function create(int $table_id): View
    {
        $vars   =   $this->s_validation->validationCreate($table_id);

        return view('orders.create', $vars);
    }

    public function store(array $data): Order
    {
        $data   =   $this->s_validation->validationStore($data);
        $dto    =   $this->s_dto->getDtoStore($data);
        $order  =   $this->s_repository->store($dto);

        $collect_detail =   collect($data['lst_detail']);
        $lst_dishes     =   $collect_detail->where('type_item', 'PLATO')->toArray();
        $lst_products   =   $collect_detail->where('type_item', 'PRODUCTO')->toArray();
        $dto_odish      =   $this->s_dto->getDtoOrderDish($lst_dishes, $order->id);
        $dto_oproduct   =   $this->s_dto->getDtoOrderProduct($lst_products, $order->id);


        $this->s_repository->storeOrderProduct($dto_oproduct);
        $this->s_repository->storeOrderDish($dto_odish);

        $this->s_reservation->store($order);

        return $order;
    }

    public function getOrderTable(int $table_id)
    {
        $item   =   $this->s_repository->getOrderTable($table_id);
        return $item;
    }
}
