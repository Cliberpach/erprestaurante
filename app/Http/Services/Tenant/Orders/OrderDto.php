<?php

namespace App\Http\Services\Tenant\Orders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Company;
use App\Models\Landlord\Customer;
use App\Models\Product;
use App\Models\Tenant\PaymentMethod;
use App\Models\Tenant\Supply\Dish\Dish;
use App\Models\Tenant\Supply\TypeDish\TypeDish;
use App\Models\Tenant\Warehouse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class OrderDto
{
    public function getDtoStore(array $data): array
    {
        $dto    =   [];

        $petty_cash_book                            =   $data['petty_cash_book'];

        $dto['petty_cash_book_id']                  =   $petty_cash_book->petty_cash_book_id;
        $dto['petty_cash_id']                       =   $petty_cash_book->petty_cash_id;
        $dto['petty_cash_name']                     =   $petty_cash_book->petty_cash_name;

        $customer                                   =   Customer::findOrFail($data['client_id']);
        $dto['customer_id']                         =   $customer->id;
        $dto['customer_type_document_abbreviation'] =   $customer->type_document_abbreviation;
        $dto['customer_document_number']            =   $customer->document_number;
        $dto['customer_name']                       =   mb_strtoupper(trim($customer->name));

        $dto['date']        =   now();

        //======== AMOUNTS ======
        $dto_amounts            =   $this->calculateAmounts($data['lst_detail']);
        $dto['igv_percentage']  =   $dto_amounts['igv_percentage'];
        $dto['total']           =   $dto_amounts['total'];
        $dto['subtotal']        =   $dto_amounts['subtotal'];
        $dto['igv']             =   $dto_amounts['igv'];

        $dto['table_id']        =   $data['table_id'];
        $dto['observation']     =   mb_strtoupper(trim($data['observation']), 'UTF-8');

        $dto['payref_id']      =   $data['payref_id'] ?? null;
        $dto['payref_name']    =   $data['payref_name'] ?? null;

        $dto['pending_order_printing']  =   'SI';
        $dto['order_print_mode']        =   'TODO';

        if ($data['mode'] == "UPDATE") {
            $dto['pending_order_printing']  =   'SI';
            $dto['order_print_mode']        =   'PARCIAL';
        }

        if (isset($data['voucher'])) {
            $user                   =   Auth::user();
            $files_route            =   Company::findOrFail(1)->files_route;
            $extension              =   $data['voucher']->getClientOriginalExtension();

            $file_name              =   uniqid() . '_voucher.' . $extension;
            $dto['payref_img_url']  =   $files_route . '/orders/payrefs/' . $file_name;
            $dto['payref_img_name'] =   $file_name;
            $dto['payref_user_id']  =   $user->id;
            $dto['payref_user_name']=   $user->name;
            $dto['payref_date']     =   now();
        }

        return $dto;
    }

    public function getDtoOrderDish(array $lst_items, int $order_id, string $mode): array
    {
        $dto    =   [];
        foreach ($lst_items as $item) {
            $_item      =   [];
            $dish       =   Dish::findOrFail($item->id);
            $type_dish  =   TypeDish::findOrFail($dish->type_dish_id);

            if (isset($item->order_detail_id)) {
                $_item['order_detail_id']   =   $item->order_detail_id;
            }

            $_item['created_at']        =   Carbon::now();
            $_item['order_id']          =   $order_id;
            $_item['programming_id']    =   $item->programming_id;
            $_item['dish_id']           =   $dish->id;
            $_item['dish_name']         =   $dish->name;
            $_item['sale_price']        =   $dish->sale_price;
            $_item['quantity']          =   $item->quantity;
            $_item['purchase_price']    =   $dish->purchase_price;
            $_item['total']             =   $dish->sale_price * $item->quantity;
            $_item['type_dish_id']      =   $dish->type_dish_id;
            $_item['type_dish_name']    =   $type_dish->name;
            $_item['observation']       =   mb_strtoupper(trim($item->observation ?? null), 'UTF-8');

            if ($mode === 'STORE') {
                $_item['detail_printed']    =   'SI';
            }
            if ($mode === 'UPDATE_NEW') {
                $_item['detail_printed']    =   'NO';
            }
            if ($mode === 'UPDATE_OLD') {
                $_item['delete_status']             =   false;
                if ($item->print_status === 'IMPRESO') {
                    $_item['print_delivery_status']     =   'ENTREGADO';
                }
            }
            $dto[]  =   $_item;
        }

        return $dto;
    }

    public function getDtoOrderProduct(array $lst_items, int $order_id, string $mode): array
    {
        $dto    =   [];
        foreach ($lst_items as $item) {
            $_item      =   [];
            $product    =   Product::findOrFail($item->id);
            $category   =   Category::findOrFail($product->category_id);
            $brand      =   Brand::findOrFail($product->brand_id);
            $warehouse  =   Warehouse::findOrFail($item->warehouse_id);


            if (isset($item->order_detail_id)) {
                $_item['order_detail_id']   =   $item->order_detail_id;
            }

            $_item['created_at']        =   Carbon::now();
            $_item['order_id']          =   $order_id;
            $_item['warehouse_id']      =   $item->warehouse_id;
            $_item['product_id']        =   $product->id;
            $_item['product_name']      =   $product->name;
            $_item['warehouse_name']    =   $warehouse->descripcion;
            $_item['sale_price']        =   $product->sale_price;
            $_item['quantity']          =   $item->quantity;
            $_item['purchase_price']    =   $product->purchase_price;
            $_item['total']             =   (float)$product->sale_price * (float)$item->quantity;
            $_item['category_id']       =   $product->category_id;
            $_item['brand_id']          =   $product->brand_id;
            $_item['category_name']     =   $category->name;
            $_item['brand_name']        =   $brand->name;
            $_item['observation']       =   mb_strtoupper(trim($item->observation ?? null), 'UTF-8');
            if ($mode === 'STORE') {
                $_item['detail_printed']    =   'SI';
            }
            if ($mode === 'UPDATE_NEW') {
                $_item['detail_printed']    =   'NO';
            }
            if ($mode === 'UPDATE_OLD') {
                $_item['delete_status']             =   false;
                if ($item->print_status === 'IMPRESO') {
                    $_item['print_delivery_status']     =   'ENTREGADO';
                }
            }
            $dto[]  =   $_item;
        }

        return $dto;
    }

    public function calculateAmounts(array $lst_items): array
    {
        $total = 0;
        $igv   = 0;
        $subtotal = 0;
        $igv_percentage = Company::find(1)->igv;

        foreach ($lst_items as $item) {
            $total  +=  $item->total;
        }

        $subtotal   =   $total / (1 + ($igv_percentage / 100));
        $igv        =   $total - $subtotal;

        return [
            'total'             =>  $total,
            'subtotal'          =>  $subtotal,
            'igv'               =>  $igv,
            'igv_percentage'    =>  $igv_percentage
        ];
    }

    public function getDtoAddPay(array $data): array
    {
        $dto    =   [];
        if (isset($data['voucher'])) {

            $payment_method         =   PaymentMethod::findOrFail($data['payment_method']);
            $dto['payref_id']       =   $payment_method->id;
            $dto['payref_name']     =   $payment_method->description;

            $user                   =   Auth::user();
            $files_route            =   Company::findOrFail(1)->files_route;
            $extension              =   $data['voucher']->getClientOriginalExtension();

            $file_name              =   uniqid() . '_voucher.' . $extension;
            $dto['payref_img_url']  =   $files_route . '/orders/payrefs/' . $file_name;
            $dto['payref_img_name'] =   $file_name;
            $dto['payref_user_id']  =   $user->id;
            $dto['payref_user_name'] =   $user->name;
            $dto['payref_date']     =   now();
        }
        return $dto;
    }
}
