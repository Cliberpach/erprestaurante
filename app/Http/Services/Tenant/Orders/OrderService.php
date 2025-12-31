<?php

namespace App\Http\Services\Tenant\Orders;

use App\Http\Controllers\UtilController;
use App\Http\Services\Tenant\Cash\PettyCashBook\PettyCashBookService;
use App\Models\Tenant\Orders\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    private OrderValidation $s_validation;
    private OrderDto $s_dto;
    private OrderRepository $s_repository;

    public function __construct(){
        $this->s_validation =   new OrderValidation();
        $this->s_dto        =   new OrderDto();
        $this->s_repository =   new OrderRepository();
    }

    public function create(int $table_id): View
    {
        $vars   =   $this->s_validation->validationCreate($table_id);

        return view('orders.create', $vars);
    }

    public function store(array $data):Order{
        $data   =   $this->s_validation->validationStore($data);
        $dto    =   $this->s_dto->getDtoStore($data);
        $order  =   $this->s_repository->insertOrder($dto);

        $collect_detail =   collect($data['lst_detail']);
        $lst_dishes     =   $collect_detail->where('type_item','PLATO')->toArray();
        $lst_products   =   $collect_detail->where('type_item','PRODUCTO')->toArray();
        $dto_odish      =   $this->s_dto->getDtoOrderDish($lst_dishes,$order->id);
        dd($dto_odish);
    }
}
