<?php

namespace App\Http\Services\Tenant\Orders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Company;
use App\Models\Landlord\Customer;
use App\Models\Product;
use App\Models\Tenant\Supply\Dish\Dish;
use App\Models\Tenant\Supply\TypeDish\TypeDish;

class OrderDto
{
    public function getDtoStore(array $data): array
    {
        $dto    =   [];

        $customer                                   =   Customer::findOrFail($data['client_id']);
        $dto['customer_id']                         =   $customer->id;
        $dto['customer_type_document_abbreviation'] =   $customer->type_document_abbreviation;
        $dto['customer_document_number']            =   $customer->document_number;
        $dto['customer_name']                       =   mb_strtoupper(trim($customer->name));

        $dto['date']        =   now();

        //======== AMOUNTS ======
        $dto_amounts        =   $this->calculateAmounts($data['lst_detail']);
        $dto['total']       =   $dto_amounts['total'];
        $dto['subtotal']    =   $dto_amounts['subtotal'];
        $dto['igv']         =   $dto_amounts['igv'];

        $dto['table_id']    =   $data['table_id'];
        $dto['observation'] =   mb_strtoupper(trim($data['observation']), 'UTF-8');

        return $dto;
    }

    public function getDtoOrderDish(array $lst_items, int $order_id): array
    {
        $dto    =   [];
        foreach ($lst_items as $item) {
            $_item      =   [];
            $dish       =   Dish::findOrFail($item->id);
            $type_dish  =   TypeDish::findOrFail($dish->type_dish_id);

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

            $dto[]  =   $_item;
        }

        return $dto;
    }

    public function getDtoOrderProduct(array $lst_items, int $order_id): array
    {
        $dto    =   [];
        foreach ($lst_items as $item) {
            $_item      =   [];
            $product    =   Product::findOrFail($item->id);
            $category   =   Category::findOrFail($product->category_id);
            $brand      =   Brand::findOrFail($product->brand_id);

            $_item['order_id']          =   $order_id;
            $_item['warehouse_id']      =   $item->warehouse_id;
            $_item['product_id']        =   $product->id;
            $_item['product_name']      =   $product->name;
            $_item['sale_price']        =   $product->sale_price;
            $_item['quantity']          =   $item->quantity;
            $_item['purchase_price']    =   $product->purchase_price;
            $_item['total']             =   $product->sale_price * $item->quantity;
            $_item['category_id']       =   $product->category_id;
            $_item['brand_id']          =   $product->brand_id;
            $_item['category_name']     =   $category->name;
            $_item['brand_name']        =   $brand->name;

            $dto[]  =   $_item;
        }

        return $dto;
    }

    public function calculateAmounts(array $lst_items): array
    {
        $total = 0;
        $igv   = 0;
        $subtotal = 0;
        $porcentaje_igv = Company::find(1)->igv;

        foreach ($lst_items as $item) {
            $total  +=  $item->total;
        }

        $subtotal   =   $total / (1 + ($porcentaje_igv / 100));
        $igv        =   $total - $subtotal;

        return [
            'total'     =>  $total,
            'subtotal'  =>  $subtotal,
            'igv'       =>  $igv
        ];
    }
}
