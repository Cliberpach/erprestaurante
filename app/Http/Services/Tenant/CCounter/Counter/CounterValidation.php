<?php

namespace App\Http\Services\Tenant\CCounter\Counter;

use App\Http\Controllers\FormatController;
use App\Http\Controllers\UtilController;
use App\Http\Services\Tenant\Orders\OrderService;
use Exception;

class CounterValidation
{
    private CounterRepository $s_repository;
    private OrderService $s_order;

    public function __construct($_s_repository, $_s_order)
    {
        $this->s_repository =   $_s_repository;
        $this->s_order      =   $_s_order;
    }

    public function validationChargeCreate(int $order_id): array
    {
        $order  =   $this->s_repository->getOrder($order_id);

        if (!$order) {
            throw new Exception('PEDIDO NO DISPONIBLE');
        }

        if ($order->order_status !== 'ACTIVO') {
            throw new Exception("EL PEDIDO SE ENCUENTRA: " . $order->status);
        }
        if ($order->status_invoice !== 'NO FACTURADO') {
            throw new Exception("EL PEDIDO YA FUE FACTURADO CON: " . $order->sale_serie . '-' . $order->sale_correlative);
        }

        $lst_detail_active      =   $this->s_order->getOrderDetail($order_id);
        $lst_detail_canceled    =   $this->s_order->getOrderDetailCanceled($order_id);
       
        $payment_methods    =   UtilController::getPaymentMethods();
        $customer_formatted =   FormatController::getFormatInitialCustomer(1);
        $invoice_types      =   UtilController::getInvoiceTypes()->whereIn('id', [65, 66, 67]);

        $vars_mdl_customer  =   UtilController::getVarsMdlCustomer();

        $vars   =   [
            'order'             =>  $order,
            'lst_detail'        =>  $lst_detail_active,
            'lst_canceled'      =>  $lst_detail_canceled,
            'payment_methods'   =>  $payment_methods,
            'customer_formatted' => $customer_formatted,
            'invoice_types'     =>  $invoice_types
        ];

        $vars = array_merge($vars, $vars_mdl_customer);

        return $vars;
    }
}
