<?php

namespace App\Http\Services\Tenant\CCounter\Counter;

use App\Http\Services\Tenant\Alerts\AlertSale\AlertSaleService;
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
        $this->s_order->setStatusInvoice($data['order_id'], 'FACTURADO', $invoice);

        $this->alertSale($data, $invoice);

        return $invoice;
    }

    public function getWaiters(): array
    {
        return $this->s_repository->getWaitersByCashier(auth()->id())->toArray();
    }

    public function changeWaiter(int $order_id, array $data): void
    {
        $this->s_validation->validationChangeWaiter($order_id);
        $this->s_repository->changeWaiter($order_id, (int) $data['waiter_id']);
    }

    public function alertSale(array $data, $invoice)
    {
        $lstAlerts  =   json_decode($data['lstAlertsSelected']);
        if (count($lstAlerts) === 0) return;

        $data_alert =   [
            'sale'          =>  $invoice,
            'lst_alerts'    =>  $lstAlerts
        ];

        $s_alert    =   new AlertSaleService();
        $s_alert->store($data_alert);
    }
}
