<?php

namespace App\Http\Services\Tenant\Consumables\Purchase;

use App\Models\Supplier;
use App\Models\Tenant\Consumables\Consumable\Consumable;
use App\Models\Tenant\Consumables\ConsumableBrand\ConsumableBrand;
use App\Models\Tenant\Consumables\ConsumableCategory\ConsumableCategory;
use App\Models\Tenant\Consumables\ConsumablePurchase\ConsumablePurchase;
use App\Models\Tenant\Maintenance\CostCenter;
use App\Models\Tenant\Sales\PaymentCondition\PaymentCondition;
use App\Models\Tenant\Warehouse;

class PurchaseDto
{

    public function getDtoStore(array $data): array
    {
        $dto    =   [];

        $supplier           =   Supplier::findOrFail($data['proveedor']);
        $warehouse          =   Warehouse::findOrFail(1);
        $cost_center        =   CostCenter::findOrFail($data['cost_center']);
        $montos             =   $this->calcularMontos($data['lst_purchase'], $data['igv_chk'] ?? null, $data['igv_value']);

        $dto['warehouse_id']                        =   $warehouse->id;
        $dto['warehouse_name']                      =   $warehouse->descripcion;
        $dto['delivery_date']                       =   $data['fecha_entrega'];
        $dto['supplier_id']                         =   $supplier->id;
        $dto['supplier_name']                       =   $supplier->name;
        $dto['supplier_type_document_abbreviation'] =   $supplier->type_document_abbreviation;
        $dto['supplier_document_number']            =   $supplier->document_number;
        $dto['currency']                            =   $data['moneda'];
        $dto['document_type']                       =   $data['tipo_doc'];
        $dto['serie']                               =   $data['serie'];
        $dto['correlative']                         =   $data['numero'];
        $dto['observation']                         =   $data['observation'];
        $dto['cost_center_id']                      =   $cost_center->id;
        $dto['cost_center_name']                    =   $cost_center->name;

        $dto['prices_with_igv'] =   isset($data['igv_chk']) ? 1 : 0;
        $dto['igv']             =   $data['igv_value'];
        $dto['subtotal']        =   $montos->subtotal;
        $dto['amount_igv']      =   $montos->monto_igv;
        $dto['total']           =   $montos->total;
        $dto['discount_cash']   =   isset($data['discount_cash']) ? true : false;

        //========= DATES =========
        $registration_date      =   now();
        $payment_condition      =   PaymentCondition::findOrFail($data['payment_condition_id']);
        $nro_days               =   (int) $payment_condition->nro_days;
        $expiration_date        =   $registration_date->copy()->addDays($nro_days);

        $dto['registration_date']       =   $registration_date;
        $dto['payment_condition_id']    =   $payment_condition->id;
        $dto['payment_condition_name']  =   $payment_condition->name;
        $dto['payment_condition_days']  =   $payment_condition->nro_days;
        $dto['expiration_date']         =   $expiration_date;

        $dto['payment_status']          =   $payment_condition->name === 'CONTADO' ? 'PAGADO' : 'PENDIENTE';
        return $dto;
    }

    public function getDtoDetail(array $lst_items, ConsumablePurchase $purchase): array
    {
        $dto_detail =   [];
        foreach ($lst_items as  $item) {
            $_item  =   [];

            $consumable                     =   Consumable::findOrFail($item->product_id);
            $brand                          =   ConsumableBrand::findOrFail($consumable->brand_id);
            $category                       =   ConsumableCategory::findOrFail($consumable->category_id);

            $_item['purchase_id']           =   $purchase->id;
            $_item['consumable_id']         =   $consumable->id;
            $_item['brand_id']              =   $brand->id;
            $_item['category_id']           =   $category->id;
            $_item['consumable_name']       =   $consumable->name;
            $_item['brand_name']            =   $brand->name;
            $_item['category_name']         =   $category->name;
            $_item['warehouse_id']          =   $purchase->warehouse_id;
            $_item['warehouse_name']        =   $purchase->warehouse_name;
            $_item['quantity']              =   $item->quantity;
            $_item['purchase_price']        =   $item->purchase_price;
            $_item['subtotal']              =   $item->quantity * $item->purchase_price;
            $_item['unit_name']             =   $consumable->unit_name;
            $_item['unit_symbol']           =   $consumable->unit_symbol;
            $dto_detail[]   =   $_item;
        }

        return $dto_detail;
    }

    public static function calcularMontos($lstPurchaseDocument, $igv_chk, $igv_value)
    {
        $subtotal   =   0;
        $monto_igv  =   0;
        $total      =   0;
        $valor_igv  =   $igv_value;

        if ($igv_chk) {
            foreach ($lstPurchaseDocument as $item) {
                $total  +=  (float)$item->total;
            }
            $subtotal    =   $total / ((100 + (float)$valor_igv) / 100);
            $monto_igv   =   $total - $subtotal;
        } else {
            //======= PRECIOS SIN IGV =======
            foreach ($lstPurchaseDocument as $item) {
                $subtotal  +=  (float)$item->total;
            }

            $monto_igv   =   ((float)$valor_igv / 100) * $subtotal;
            $total       =   $subtotal + $monto_igv;
        }

        return (object)[
            'subtotal' => $subtotal,
            'monto_igv' => $monto_igv,
            'total' => $total
        ];
    }
}
