<?php

namespace App\Http\Services\Tenant\CCounter\Counter;

use App\Http\Services\Tenant\Orders\OrderService;
use App\Http\Services\Tenant\Sale\Sale\SaleService;
use App\Models\Tenant\Sales\Sale\Sale;

class CounterService
{
    private CounterValidation $s_validation;
    private OrderService $s_order;
    private CounterRepository $s_repository;
    private SaleService $s_sale;

    public function __construct()
    {
        $this->s_repository =   new CounterRepository();
        $this->s_order      =   new OrderService();
        $this->s_validation =   new CounterValidation($this->s_repository, $this->s_order);
        $this->s_sale       =   new SaleService();
    }

    public function chargeCreate(int $order)
    {
        $vars   =   $this->s_validation->validationChargeCreate($order);
        return view('cashier_counter.counter.charge', $vars);
    }

    public function storeInvoice(array $data): Sale
    {
        $invoice    =   $this->s_sale->storeFromCOrder($data);
        $this->s_order->setStatusInvoice($data['order_id'], 'FACTURADO',$invoice);
        return $invoice;
    }
}
