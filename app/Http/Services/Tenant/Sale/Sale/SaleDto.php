<?php

namespace App\Http\Services\Tenant\Sale\Sale;

use App\Http\Controllers\Tenant\NumberToLettersController;
use App\Models\Tenant\PaymentMethod;
use App\Models\Tenant\Sales\Sale\Sale;
use App\Models\Tenant\WorkShop\Service;

class SaleDto
{
    public function getDtoStoreFromOrder(array $data)
    {
        $dto    =   [];

        $customer                           =   $data['customer'];
        $dto['customer_id']                 =   $customer->id;
        $dto['customer_name']               =   $customer->name;
        $dto['customer_type_document']      =   $customer->type_document_abbreviation;
        $dto['customer_document_number']    =   $customer->document_number;
        $dto['customer_document_code']      =   $customer->type_document_code;
        $dto['customer_phone']              =   $customer->phone;

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

            $s_dto['sale_id']                   =     $sale->id;
            $s_dto['warehouse_id']              =     $item->warehouse_id;
            $s_dto['warehouse_name']            =     $item->warehouse_name;
            $s_dto['product_id']                =     $item->product_id;
            $s_dto['category_id']               =     $item->category_id;
            $s_dto['brand_id']                  =     $item->brand_id;
            $s_dto['product_name']              =     $item->product_name;
            $s_dto['category_name']             =     $item->category_name;
            $s_dto['brand_name']                =     $item->brand_name;

            $s_dto['quantity']                  =   $item->quantity;
            $s_dto['purchase_price']            =   $item->purchase_price;
            $s_dto['sale_price']                =   $item->sale_price;
            $s_dto['total']                     =   $item->total;

            $s_dto['mto_valor_unitario']     =   (float)($item->sale_price / 1.18);
            $s_dto['mto_valor_venta']        =   (float)($sale->total / 1.18);
            $s_dto['mto_base_igv']           =   (float)($sale->total / 1.18);
            $s_dto['porcentaje_igv']         =   $sale->igv_percentage;
            $s_dto['igv']                    =   (float)($sale->total) - (float)($sale->total / 1.18);
            $s_dto['tip_afe_igv']            =   10;
            $s_dto['total_impuestos']        =   (float)($sale->total) - (float)($sale->total / 1.18);
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

            $_item['mto_valor_unitario']     =   (float)($item->sale_price / 1.18);
            $_item['mto_valor_venta']        =   (float)($sale->total / 1.18);
            $_item['mto_base_igv']           =   (float)($sale->total / 1.18);
            $_item['porcentaje_igv']         =   $sale->igv_percentage;
            $_item['igv']                    =   (float)($sale->total) - (float)($sale->total / 1.18);
            $_item['tip_afe_igv']            =   10;
            $_item['total_impuestos']        =   (float)($sale->total) - (float)($sale->total / 1.18);
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
}
