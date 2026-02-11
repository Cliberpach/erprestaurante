<?php

namespace App\Http\Services\Tenant\Sale\Sale;

use App\Http\Controllers\Tenant\NumberToLettersController;
use App\Models\Company;
use App\Models\Landlord\Customer;
use App\Models\Tenant\PaymentMethod;
use App\Models\Tenant\Sales\Sale\Sale;
use App\Models\Tenant\WorkShop\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SaleDto
{
    public function getDtoStore(
        object $validated_data,
        object $amounts,
    ): array {
        $dto = [];
        $legend             =   $validated_data->legend;
        $data_correlative   =   $validated_data->data_correlative;

        $dto['warehouse_id']               = 1;
        $dto['warehouse_name']             = 'CENTRAL';
        //======= CLIENTE =======
        $dto['customer_id']                = $validated_data->customer->id;
        $dto['customer_name']              = $validated_data->customer->name;
        $dto['customer_type_document']     = $validated_data->customer->type_document_abbreviation;
        $dto['customer_document_number']   = $validated_data->customer->document_number;
        $dto['customer_document_code']     = $validated_data->customer->type_document_code;
        $dto['customer_phone']             = $validated_data->customer->phone;

        //======= USUARIO REGISTRADOR =======
        $dto['user_recorder_id']            = Auth::user()->id;
        $dto['user_recorder_name']          = Auth::user()->name;

        //====== CAJA / MOVIMIENTO ======
        $dto['petty_cash_id']               = $validated_data->petty_cash->petty_cash_id;
        $dto['petty_cash_name']             = $validated_data->petty_cash->name;
        $dto['petty_cash_book_id']          = $validated_data->petty_cash->petty_cash_book_id;

        //======== TIPO DE VENTA ======
        $dto['type_sale_id']                = $validated_data->type_sale_id;
        $dto['type_sale_code']              = $validated_data->type_sale_code;
        $dto['type_sale_name']              = $validated_data->type_sale_name;

        //====== MONTOS ======
        $dto['igv_percentage']              = $validated_data->igv_percentage;
        $dto['subtotal']                    = $amounts->subtotal;
        $dto['igv_amount']                  = $amounts->igv_amount;
        $dto['total']                       = $amounts->total;
        $dto['legend']                      = $legend;

        //======== SERIE Y CORRELATIVO =======
        $dto['correlative']                 = $data_correlative->correlative;
        $dto['serie']                       = $data_correlative->serie;

        //========== FECHAS ========
        $dto['expiration_date']             = $validated_data->expiration_date;
        $dto['registration_date']           = $validated_data->registration_date;

        $dto['payment_condition_id']        = $validated_data->payment_condition->id;
        $dto['payment_condition_name']      = $validated_data->payment_condition->name;
        $dto['payment_condition_days']      = $validated_data->payment_condition->nro_days;

        $dto['payment_status']              = $validated_data->payment_condition->name === 'CONTADO' ? 'PAGADO' : 'PENDIENTE';

        return $dto;
    }

    public function formatDetailSale(array $lst_products): array
    {
        $lst_formatted   =   array_map(function ($item) {
            return (object) [
                'warehouse_id'      =>  1,
                'warehouse_name'    =>  'CENTRAL',
                'product_id'        =>  $item->id,
                'category_id'       =>  $item->category_id,
                'brand_id'          =>  $item->brand_id,
                'product_name'      =>  $item->name,
                'category_name'     =>  $item->category_name,
                'brand_name'        =>  $item->brand_name,
                'quantity'          =>  $item->cant,
                'purchase_price'    =>  $item->purchase_price,
                'sale_price'        =>  $item->sale_price,
                'total'             =>  $item->sale_price * $item->cant
            ];
        }, $lst_products);
        return $lst_formatted;
    }

    public function formatPays(array $lst_pays): array
    {
        $lst_formatted  =   array_map(function ($item) {
            return (object) [
                'paymentId'     => $item->method_pay,
                'amount' => $item->amount,
            ];
        }, $lst_pays);
        return $lst_formatted;
    }

    public function getDtoStoreFromOrder(array $data)
    {
        $dto    =   [];

        $dto['warehouse_id']                =   1;
        $dto['warehouse_name']              =   'CENTRAL';

        $customer                           =   $data['customer'];
        $dto['customer_id']                 =   $customer->id;
        $dto['customer_name']               =   $customer->name;
        $dto['customer_type_document']      =   $customer->type_document_abbreviation;
        $dto['customer_document_number']    =   $customer->document_number;
        $dto['customer_document_code']      =   $customer->type_document_code;
        $dto['customer_phone']              =   $customer->phone;
        $dto['customer_address']            =   $customer->address;

        $cash_book                          =   $data['cash_book'];
        $dto['petty_cash_id']               =   $cash_book->petty_cash_id;
        $dto['petty_cash_name']             =   $cash_book->petty_cash_name;
        $dto['petty_cash_book_id']          =   $cash_book->petty_cash_book_id;

        $invoice                            =   $data['invoice'];
        $dto['type_sale_id']                =   $invoice->id;
        $dto['type_sale_code']              =   $invoice->symbol;
        $dto['type_sale_name']              =   $invoice->name;

        $dto['igv_percentage']              =   $data['order']->igv_percentage;
        $dto['subtotal']                    =   $data['order']->subtotal;
        $dto['igv_amount']                  =   $data['order']->igv;
        $dto['total']                       =   $data['order']->total;

        $legend                 =   NumberToLettersController::numberToLetters($dto['total']);
        $dto['legend']          =   $legend;


        $data_correlative       =   $data['correlative'];
        $dto['correlative']     =   $data_correlative->correlative;
        $dto['serie']           =   $data_correlative->serie;

        $dto['type']            =   "PRODUCTOS";

        $dto['order_id']        =   $data['order'] ? $data['order']->id : null;
        $dto['pay_status']      =   'PAGADO';
        $dto['change_pay']      =   $data['change'];

        $dto['expiration_date']             = Carbon::now();
        $dto['registration_date']           = Carbon::now();

        $dto['payment_condition_id']        = 1;
        $dto['payment_condition_name']      = "CONTADO";
        $dto['payment_condition_days']      = 0;

        $dto['payment_status']              = "PAGADO";

        return $dto;
    }

    public function getDtoServices(array $data, Sale $sale)
    {
        $dto    =   [];

        foreach ($data as $item) {
            $service                          =     Service::findOrFail($item->id);
            $s_dto['sale_document_id']        =     $sale->id;
            $s_dto['service_id']              =     $service->id;
            $s_dto['service_code']            =     $service->id;
            $s_dto['service_unit']            =     'NIU';
            $s_dto['service_description']     =     $service->id;
            $s_dto['service_name']            =     $service->name;
            $s_dto['quantity']                =     $item->quantity;
            $s_dto['sale_price']              =     $item->sale_price;
            $s_dto['amount']                  =     $item->quantity * $item->sale_price;

            $s_dto['mto_valor_unitario']     =   (float)($item->sale_price / 1.18);
            $s_dto['mto_valor_venta']        =   (float)($s_dto['amount'] / 1.18);
            $s_dto['mto_base_igv']           =   (float)($s_dto['amount'] / 1.18);
            $s_dto['porcentaje_igv']         =   $sale->igv_percentage;
            $s_dto['igv']                    =   (float)($s_dto['amount']) - (float)($s_dto['amount'] / 1.18);
            $s_dto['tip_afe_igv']            =   10;
            $s_dto['total_impuestos']        =   (float)($s_dto['amount']) - (float)($s_dto['amount'] / 1.18);
            $s_dto['mto_precio_unitario']    =   (float)($item->sale_price);

            $dto[]  =   $s_dto;
        }

        return $dto;
    }

    public function getDtoProducts($data, Sale $sale)
    {
        $dto    =   [];

        foreach ($data as $item) {

            $s_dto      =   [];
            $factor     =   (100 + $sale->igv_percentage) / 100;

            $s_dto['created_at']                =   Carbon::now();
            $s_dto['sale_id']                   =   $sale->id;
            $s_dto['warehouse_id']              =   $item->warehouse_id;
            $s_dto['warehouse_name']            =   $item->warehouse_name;
            $s_dto['product_id']                =   $item->product_id;
            $s_dto['category_id']               =   $item->category_id;
            $s_dto['brand_id']                  =   $item->brand_id;
            $s_dto['product_name']              =   $item->product_name;
            $s_dto['category_name']             =   $item->category_name;
            $s_dto['brand_name']                =   $item->brand_name;

            $s_dto['quantity']                  =   $item->quantity;
            $s_dto['purchase_price']            =   $item->purchase_price;
            $s_dto['sale_price']                =   $item->sale_price;
            $s_dto['total']                     =   $item->total;

            $s_dto['mto_valor_unitario']     =   (float)($item->sale_price / $factor);
            $s_dto['mto_valor_venta']        =   (float)($item->total / $factor);
            $s_dto['mto_base_igv']           =   (float)($item->total / $factor);
            $s_dto['porcentaje_igv']         =   $sale->igv_percentage;
            $s_dto['igv']                    =   (float)($item->total) - (float)($item->total / $factor);
            $s_dto['tip_afe_igv']            =   10;
            $s_dto['total_impuestos']        =   (float)($item->total) - (float)($item->total / $factor);
            $s_dto['mto_precio_unitario']    =   (float)($item->sale_price);

            $dto[]  =   $s_dto;
        }

        return $dto;
    }

    public function getDtoSaleDish($lst_items, Sale $sale): array
    {
        $dto    =   [];
        foreach ($lst_items as $item) {
            $_item      =   [];
            $factor     =   (100 + $sale->igv_percentage) / 100;

            $_item['created_at']        =   Carbon::now();
            $_item['sale_id']           =   $sale->id;
            $_item['programming_id']    =   $item->programming_id;
            $_item['dish_id']           =   $item->dish_id;
            $_item['dish_name']         =   $item->dish_name;
            $_item['sale_price']        =   $item->sale_price;
            $_item['quantity']          =   $item->quantity;
            $_item['purchase_price']    =   $item->purchase_price;
            $_item['total']             =   $item->total;
            $_item['type_dish_id']      =   $item->type_dish_id;
            $_item['type_dish_name']    =   $item->type_dish_name;
            $_item['observation']       =   mb_strtoupper(trim($item->observation ?? null), 'UTF-8');

            $_item['mto_valor_unitario']     =   (float)($item->sale_price / $factor);
            $_item['mto_valor_venta']        =   (float)($item->total / $factor);
            $_item['mto_base_igv']           =   (float)($item->total / $factor);
            $_item['porcentaje_igv']         =   $sale->igv_percentage;
            $_item['igv']                    =   (float)($item->total) - (float)($item->total / $factor);
            $_item['tip_afe_igv']            =   10;
            $_item['total_impuestos']        =   (float)($item->total) - (float)($item->total / $factor);
            $_item['mto_precio_unitario']    =   (float)($item->sale_price);

            $dto[]  =   $_item;
        }

        return $dto;
    }

    public function getDtoPays(array $lst_pays, Sale $sale): array
    {
        $dto    =   [];
        foreach ($lst_pays as $item) {
            $_item  =   [];

            if ($item->amount == 0) continue;
            $payment    =   PaymentMethod::findOrFail($item->paymentId);

            $_item['payment_method_id']     =   $payment->id;
            $_item['payment_method_name']   =   $payment->description;
            $_item['amount']                =   $item->amount;
            $_item['sale_id']               =   $sale->id;
            $dto[]                          =   $_item;
        }
        return $dto;
    }

    public function getDtoConvert(array $data): array
    {
        $dto                =   $data['sale']->toArray();
        $data_correlative   =   $data['correlative'];
        $invoice            =   $data['invoice'];
        $customer           =   $data['customer'];

        unset(
            $dto['id'],
            $dto['sunat_status'],
            $dto['response_cdrZip'],
            $dto['response_success'],
            $dto['response_error_code'],
            $dto['response_error_message'],
            $dto['cdr_response_id'],
            $dto['cdr_response_code'],
            $dto['cdr_response_description'],
            $dto['cdr_response_notes'],
            $dto['cdr_response_reference'],
            $dto['ruta_cdr'],
            $dto['ruta_xml'],
            $dto['ruta_qr'],
            $dto['change_pay'],
            $dto['created_at'],
            $dto['updated_at']
        );
        $dto['status']                          =   'ACTIVO';

        $dto['type_sale_id']                    =   $invoice->id;
        $dto['type_sale_code']                  =   $invoice->symbol;
        $dto['type_sale_name']                  =   $invoice->name;

        $dto['customer_id']                 =   $customer->id;
        $dto['customer_name']               =   $customer->name;
        $dto['customer_type_document']      =   $customer->type_document_abbreviation;
        $dto['customer_document_number']    =   $customer->document_number;
        $dto['customer_document_code']      =   $customer->type_document_code;
        $dto['customer_phone']              =   $customer->phone;

        $dto['correlative']    =   $data_correlative->correlative;
        $dto['serie']          =   $data_correlative->serie;

        return $dto;
    }

    public function getDtoDetailConvert(array $data, int $sale_id): array
    {
        return array_map(function ($item) use ($sale_id) {
            $item['sale_id'] = $sale_id;

            unset(
                $item['id'],
                $item['created_at'],
                $item['updated_at']
            );

            return $item;
        }, $data);
    }

    public function getDtoInvoicing(Sale $sale, $lst_detail): array
    {
        $dto            =   [];
        $customer_dto   =   [];
        $company        =   Company::findOrFail(1);

        //======== CUSTOMER DATA ==========
        $customer_dto['tipoDoc']     =   $sale->customer_document_code;
        $customer_dto['numDoc']      =   $sale->customer_document_number;
        $customer_dto['rznSocial']   =   $sale->customer_name;
        $customer_dto['telephone']   =   $sale->customer_phone;

        $customer                    =   Customer::findOrFail($sale->customer_id);
        $customer_dto['address']     =   $customer->address;
        $customer_dto['email']       =   $customer->email;

        //========= FACTURA MASTER =========
        $dto['ublVersion']      =   '2.1';
        $dto['fecVencimiento']  =   $sale->created_at;
        $dto['tipoOperacion']   =   '0101';
        $dto['tipoDoc']         =   $sale->type_sale_code;
        $dto['serie']           =   $sale->serie;
        $dto['correlativo']     =   $sale->correlative;
        $dto['fechaEmision']    =   $sale->created_at;
        $dto['formaPago']       =   'CONTADO';
        $dto['tipoMoneda']      =   'PEN';
        $dto['company']         =   1;
        $dto['client']          =   $customer_dto;
        $dto['mtoOperGravadas'] =   $sale->subtotal;
        $dto['mtoIGV']          =   $sale->igv_amount;
        $dto['totalImpuestos']  =   $sale->igv_amount;
        $dto['valorVenta']      =   $sale->subtotal;
        $dto['subTotal']        =   $sale->total;
        $dto['mtoImpVenta']     =   $sale->total;

        //======= DETAIL ===========
        $details =   [];
        foreach ($lst_detail as $item) {
            $_item  =   [];
            $_item['codProducto']       =   $item->item_id . '-' . $item->item_name;
            $_item['unidad']            =   'NIU';
            $_item['descripcion']       =   $item->item_name;
            $_item['cantidad']          =   $item->quantity;
            $_item['mtoValorUnitario']  =   $item->mto_valor_unitario;
            $_item['mtoValorVenta']     =   $item->mto_valor_venta;
            $_item['mtoBaseIgv']        =   $item->mto_base_igv;
            $_item['porcentajeIgv']     =   $item->porcentaje_igv;
            $_item['igv']               =   $item->igv;
            $_item['tipAfeIgv']         =   $item->tip_afe_igv;
            $_item['totalImpuestos']    =   $item->total_impuestos;
            $_item['mtoPrecioUnitario'] =   $item->mto_precio_unitario;

            $details[]   =   $_item;
        }

        $dto['details'] =   $details;
        $dto['legends'] =   $sale->legend;

        $dto['company'] =   $company;
        return $dto;
    }
}
