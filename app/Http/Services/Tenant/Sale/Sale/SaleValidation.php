<?php

namespace App\Http\Services\Tenant\Sale\Sale;

use App\Http\Controllers\UtilController;
use App\Http\Services\Tenant\Cash\PettyCashBook\PettyCashBookService;
use App\Http\Services\Tenant\Orders\OrderService;
use App\Models\Company;
use App\Models\Landlord\Customer;
use App\Models\Landlord\GeneralTable\GeneralTableDetail;
use App\Models\Tenant\Orders\Order;
use App\Models\Tenant\Sales\PaymentCondition\PaymentCondition;
use App\Models\Tenant\Sales\Sale\Sale;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleValidation
{
    private PettyCashBookService $s_cash;
    private OrderService $s_order;
    private SaleRepository $s_repository;

    public function __construct($sale_repository)
    {
        $this->s_cash           =   new PettyCashBookService();
        $this->s_order          =   new OrderService();
        $this->s_repository     =   $sale_repository;
    }

    public function validationStore($data): object
    {
        //====== VALIDANDO USUARIO REGISTRADOR DEBE EXISTIR ======
        $user_recorder  =   Auth::user();

        if (!$user_recorder) {
            throw new Exception("EL USUARIO REGISTRADOR NO EXISTE EN LA BD!!!");
        }

        //=========== CAJA ==========
        $cash_service   =   new PettyCashBookService();
        $petty_cash     =   $cash_service->getCashBookUser($user_recorder->id);
        if (!$petty_cash) {
            throw new Exception("El usuario no forma parte de una Caja aperturada!!!");
        }

        //======= VALIDACION TIPO DE VENTA Y CLIENTE =========
        $type_sale      =   GeneralTableDetail::findOrFail($data['type_sale']);
        $customer       =   Customer::findOrFail($data['customer_id']);

        //======== RUC Y BOLETA ======
        if ($customer->type_document_abbreviation === 'RUC' && $type_sale->id == '3') {
            throw new Exception("NO SE PERMITEN BOLETAS DE VENTA CON RUC!!!");
        }

        //======== DNI Y FACTURA ======
        if ($customer->type_document_abbreviation === 'DNI' && $type_sale->id == '1') {
            throw new Exception("NO SE PERMITEN FACTURAS DE VENTA CON DNI!!!");
        }

        //======= VALIDANDO DETALLE DE LA VENTA =======
        $lstSale    =   json_decode($data['lstSale']);
        if (count($lstSale) === 0) {
            throw new Exception("EL DETALLE DE LA VENTA ESTÁ VACÍO!!!");
        }

        //====== VALIDANDO IGV PORCENTAJE DE LA COMPAÑIA =====
        $company    =   Company::find(1);
        if ($data['igv_percentage'] != $company->igv) {
            throw new Exception("EL PORCENTAJE DE IGV DEL DOCUMENTO DE VENTA NO CORRESPONDE AL DE LA EMPRESA!!!");
        }

        //========= PAYMENT CONDITION =========
        $have_module_customer_accounts  =   UtilController::haveModuleCustomerAccounts();
        if (!$have_module_customer_accounts && $data['payment_condition_id'] != 1) {
            throw new Exception("No tiene permitido ventas al crédito");
        }

        $registration_date      =   now();
        $payment_condition      =   PaymentCondition::findOrFail($data['payment_condition_id']);
        $nro_days               =   (int) $payment_condition->nro_days;
        $expiration_date        =   $registration_date->copy()->addDays($nro_days);

        $lst_pays               =   json_decode($data['lstPays']);
        $this->validationLstProducts($lstSale);

        return (object)[
            'customer'          =>  $customer,
            'petty_cash'        =>  $petty_cash,
            'type_sale_id'      =>  $type_sale->id,
            'type_sale_code'    =>  $type_sale->symbol,
            'type_sale_name'    =>  $type_sale->name,
            'igv_percentage'    =>  $data['igv_percentage'],
            'lstSale'           =>  $lstSale,
            'type'              =>  'PRODUCTOS',

            'expiration_date'   =>  $expiration_date,
            'registration_date' =>  $registration_date,
            'payment_condition' =>  $payment_condition,

            'lst_pays'          =>  $lst_pays
        ];
    }

    public function validationAmounts($amounts, $data)
    {
        if ($amounts->total > 700 && $data->type_sale_code == '03' && $data->customer->id === 1) {
            throw new Exception("Se require DNI del cliente en boletas con monto mayor a 700 soles");
        }
    }

    public static function validationLstPays(array $lstPays, object $amounts): array
    {

        $methodPays =   array_column($lstPays, 'method_pay');

        if (count($lstPays) === 0) {
            throw new Exception("El listado de pagos está vacío!!!");
        }

        if (count($lstPays) > 2) {
            throw new Exception("Solo se aceptan 2 pagos como máximo!!!");
        }

        if (count($methodPays) !== count(array_unique($methodPays))) {
            throw new Exception("Los métodos de pago no pueden repetirse");
        }

        $totalAmount    =   0;
        $indexPay       =   0;
        foreach ($lstPays as $pay) {
            $indexPay++;
            $existsPaymentMethod = DB::table('payment_methods')->where('id', $pay->method_pay)->exists();
            if (!$existsPaymentMethod) {
                throw new Exception("NO EXISTE EL " . $indexPay . '° MÉTODO DE PAGO EN LA BD!!!');
            }

            if ((float) $pay->amount <= 0 || !filter_var($pay->amount, FILTER_VALIDATE_FLOAT)) {
                throw new Exception("Los montos deben ser valores enteros,decimales mayores a 0");
            }
            $totalAmount += (float) $pay->amount;
        }

        if (round($totalAmount, 2) !== round((float) $amounts->total, 2)) {
            throw new Exception("La suma de los pagos no coincide con el total.");
        }

        $lstPays[]  =   (object)['method_pay' => null, 'amount' => null];

        return $lstPays;
    }

    public static function validationStoreFromOrder($data): array
    {
        //======= VALIDACION TIPO DE VENTA Y CLIENTE =========
        $type_sale      =   $data['invoice_type'];
        $customer_id    =   $data['client_id'];

        $customer       =   Customer::findOrFail($customer_id);
        $invoice_tpye   =   GeneralTableDetail::findOrFail($type_sale);

        //======== RUC Y BOLETA ======
        if ($customer->type_document_abbreviation === 'RUC' && $invoice_tpye->parameter === 'B') {
            throw new Exception("NO SE PERMITEN BOLETAS DE VENTA CON RUC!!!");
        }

        //======== DNI Y FACTURA ======
        if ($customer->type_document_abbreviation === 'DNI' && $invoice_tpye->parameter === 'F') {
            throw new Exception("NO SE PERMITEN FACTURAS DE VENTA CON DNI!!!");
        }

        //======= VALIDANDO DETALLE DE LA VENTA =======
        $lst_products   =   $data['lst_products'];
        $lst_services   =   $data['lst_services'];

        if (count($lst_products) === 0 && count($lst_services) === 0) {
            throw new Exception("EL DETALLE DE LA VENTA ESTÁ VACÍO!!!");
        }

        //====== VALIDANDO IGV PORCENTAJE DE LA COMPAÑIA =====
        $company    =   Company::find(1);

        $data['customer']       =   $customer;
        $data['invoice_type']   =   $invoice_tpye;
        $data['igv_percentage'] =   $company->igv;
        $data['lst_products']   =   $lst_products;
        $data['lst_services']   =   $lst_services;
        $data['type']           =   'PRODUCTOS';

        return $data;
    }

    public function vStoreFromCOrder(array $data): array
    {
        $customer           =   Customer::findOrFail($data['customer_id']);
        $invoice            =   GeneralTableDetail::findOrFail($data['invoice_id']);
        $order              =   Order::findOrFail($data['order_id']);
        $user               =   Auth::user();
        $cash_book          =   $this->s_cash->getCashBookUser($user->id);
        $lst_pays           =   json_decode($data['lst_pays']);
        $amounts            =   json_decode($data['lst_amounts']);
        $data['amounts']    =   $amounts;
        $data['order']      =   $order;
        $data['lst_pays']   =   $lst_pays;

        $roles  =   Auth::user()->getRoleNames();
        if (!$roles->contains('CAJERO')) {
            throw new Exception('No tienes rol de Cajero para acceder a esta funcionalidad');
        }

        if ($order->status !== 'ACTIVO') {
            throw new Exception("EL PEDIDO SE ENCUENTRA: " . $order->status);
        }
        if ($order->status_invoice !== 'NO FACTURADO') {
            throw new Exception("EL PEDIDO YA FUE FACTURADO");
        }

        if (!$cash_book) {
            throw new Exception("DEBES PERTENECER A UNA CAJA APERTURADA");
        }

        if ($order->petty_cash_book_id !== $cash_book->petty_cash_book_id) {
            throw new Exception("Debes pertenecer a la misma caja del pedido para poder facturarlo.");
        }

        //======== RUC Y BOLETA ======
        if ($customer->type_document_abbreviation === 'RUC' && $invoice->id == 65) {
            throw new Exception("NO SE PERMITEN BOLETAS DE VENTA CON RUC!!!");
        }

        //======== DNI Y FACTURA ======
        if ($customer->type_document_abbreviation === 'DNI' && $invoice->id == 66) {
            throw new Exception("NO SE PERMITEN FACTURAS DE VENTA CON DNI!!!");
        }

        //======== BOLETAS > 700 - DNI OBLIGATORIO ==========
        if ($invoice->id == 65 && (float)$order->total > 700 && $customer->id == 1) {
            throw new Exception("Se require DNI del cliente en boletas con monto mayor a 700 soles");
        }

        //======== PAGO =========
        $data_amounts           =   $this->validationCLstPays($data);

        $order_dishes           =   $this->s_order->getOrderDishes($data['order_id']);
        $order_products         =   $this->s_order->getOrderProducts($data['order_id']);

        $data['customer']       =   $customer;
        $data['invoice']        =   $invoice;
        $data['cash_book']      =   $cash_book;
        $data['user']           =   $user;
        $data['order_dishes']   =   $order_dishes;
        $data['order_products'] =   $order_products;
        $data['total_pay']      =   $data_amounts->total_pay;
        $data['change']         =   $data_amounts->change;
        $data['igv_percentage'] =   $order->igv_percentage;
        $data['order']          =   $order;
        return $data;
    }

    public function validationCLstPays($data)
    {
        $lst_pays       =   $data['lst_pays'];
        $amounts        =   $data['amounts'];
        $order          =   $data['order'];

        $c_pays         =   collect($lst_pays)->where('amount', '>', 0);
        $payTotal       =   $c_pays->sum('amount');
        $has_negative   =   $c_pays->where('amount', '<', 0)->first();
        $has_repeats    =   $c_pays->groupBy('paymentId')->filter(fn($items) => $items->count() > 1)->isNotEmpty();
        $new_total      =   $amounts->discount && (float)$amounts->discount > 0 ? (float)$order->total - (float)$amounts->discount : $order->total;

        if ($payTotal == 0) {
            throw new Exception('NO INGRESASTE NINGÚN PAGO');
        }
        if ($payTotal < 0) {
            throw new Exception("NO SE ACEPTAN PAGOS NEGATIVOS");
        }
        if ((float)$payTotal < (float)$new_total) {
            throw new Exception("EL PAGO ES MENOR AL TOTAL DEL PEDIDO");
        }
        if ($has_negative) {
            throw new Exception("NO SE ADMITEN MONTOS MENORES A 0");
        }
        if ($has_repeats) {
            throw new Exception("SE DETECTARON MÉTODOS DE PAGO REPETIDOS");
        }

        //========= VUELTO ========
        $change =   (float)$payTotal - (float)$new_total;
        if ($change < 0) {
            throw new Exception("EL VUELTO NO PUEDE SER NEGATIVO");
        }
        return (object)[
            'change'    =>  $change,
            'total_pay' =>  $new_total
        ];
    }

    public function validationConvert(array $data): array
    {
        $customer       =   Customer::findOrFail($data['customer_id']);
        $invoice        =   GeneralTableDetail::findOrFail($data['invoice_id']);
        $sale           =   Sale::findOrFail($data['sale_id']);

        if ($sale->type_sale_id !== 67) {
            throw new Exception("SOLO SE PERMITE CONVERSIÓN DE NOTAS DE VENTA A BOLETA O FACTURA");
        }

        if ($sale->converted_to_id) {
            throw new Exception("ESTA VENTA YA FUE CONVERTIDA A: " . $sale->converted_to_serie . ", NO SE PUEDE RECONVERTIR");
        }
        if ($sale->converted_from_id) {
            throw new Exception("ESTA VENTA FUE CONVERTIDA DE: " . $sale->converted_from_id . ", NO SE PUEDE RECONVERTIR");
        }

        //======== RUC Y BOLETA ======
        if ($customer->type_document_abbreviation === 'RUC' && $invoice->id == 65) {
            throw new Exception("NO SE PERMITEN BOLETAS DE VENTA CON RUC!!!");
        }

        //======== DNI Y FACTURA ======
        if ($customer->type_document_abbreviation === 'DNI' && $invoice->id == 66) {
            throw new Exception("NO SE PERMITEN FACTURAS DE VENTA CON DNI!!!");
        }

        $sale_products      =   $this->s_repository->getSaleProducts($data['sale_id']);
        $sale_dishes        =   $this->s_repository->getSaleDishes($data['sale_id']);

        $data['sale']           =   $sale;
        $data['customer']       =   $customer;
        $data['invoice']        =   $invoice;
        $data['sale_dishes']    =   $sale_dishes;
        $data['sale_products']  =   $sale_products;

        return $data;
    }

    public function validationSend(Sale $sale)
    {
        if ($sale->sunat_status === 'ACEPTADO') {
            throw new Exception('DOCUMENTO DE VENTA: ' . $sale->serie . '-' . $sale->correlative . ', YA FUE ACEPTADO POR SUNAT');
        }
        if ($sale->sunat_status === 'ANULADO') {
            throw new Exception('DOCUMENTO DE VENTA: ' . $sale->serie . '-' . $sale->correlative . ', SE ENCUENTRA ANULADO');
        }
        if ($sale->sunat_status === 'ANULADO PARCIAL') {
            throw new Exception('DOCUMENTO DE VENTA: ' . $sale->serie . '-' . $sale->correlative . ', SE ENCUENTRA ANULADO PARCIALMENTE');
        }
    }

    public function validationAnnular(Sale $sale)
    {
        if ($sale->sunat_status === 'PENDIENTE') {
            throw new Exception('DOCUMENTO DE VENTA: ' . $sale->serie . '-' . $sale->correlative . ', AÚN NO HA SIDO ENVIADO A SUNAT');
        }
        if ($sale->sunat_status === 'RECHAZADO') {
            throw new Exception('DOCUMENTO DE VENTA: ' . $sale->serie . '-' . $sale->correlative . ', AÚN NO HA SIDO ACEPTADO EN SUNAT');
        }
        if ($sale->sunat_status === 'ANULADO') {
            throw new Exception('DOCUMENTO DE VENTA: ' . $sale->serie . '-' . $sale->correlative . ', YA FUE ANULADO');
        }
        if ($sale->sunat_status === 'ANULADO PARCIAL') {
            throw new Exception('DOCUMENTO DE VENTA: ' . $sale->serie . '-' . $sale->correlative . ', YA FUE ANULADO');
        }
    }

    public function validationLstProducts(array $lst_items)
    {
        $lst_ids    =   collect($lst_items)->pluck('id')->values()->toArray();
        $warehouses_bd   =   DB::table('warehouse_products as wp')
            ->where('wp.warehouse_id', 1)
            ->whereIn('wp.product_id', $lst_ids)
            ->get();

        foreach ($lst_items as $item) {
            $warehouse = $warehouses_bd->firstWhere('product_id', $item->id);

            if (!$warehouse) {
                throw new Exception("Producto {$item->name} no existe en almacén");
            }
            if ((float)$warehouse->stock < (float)$item->cant) {
                throw new Exception("Stock insuficiente," . $item->name . "stock actual: " . $warehouse->stock . ", Cantidad requerida: " . $item->cant);
            }
        }
    }
}
