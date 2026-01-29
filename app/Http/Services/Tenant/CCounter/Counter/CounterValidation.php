<?php

namespace App\Http\Services\Tenant\CCounter\Counter;

use App\Http\Controllers\FormatController;
use App\Http\Controllers\UtilController;
use App\Http\Services\Tenant\Orders\OrderService;
use App\Models\CompanyInvoice;
use App\Models\Department;
use App\Models\District;
use App\Models\Province;
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
            throw new Exception("EL PEDIDO YA FUE FACTURADO");
        }

        $lst_detail         =   $this->s_order->getOrderDetail($order_id);
        $payment_methods    =   UtilController::getPaymentMethods();
        $customer_formatted =   FormatController::getFormatInitialCustomer(1);
        $invoice_types      =   UtilController::getInvoiceTypes();

        $vars_mdl_customer  =   UtilController::getVarsMdlCustomer();

        $vars   =   [
            'order'             =>  $order,
            'lst_detail'        =>  $lst_detail,
            'payment_methods'   =>  $payment_methods,
            'customer_formatted' => $customer_formatted,
            'invoice_types'     =>  $invoice_types
        ];

        $vars = array_merge($vars, $vars_mdl_customer);

        return $vars;
    }
}
