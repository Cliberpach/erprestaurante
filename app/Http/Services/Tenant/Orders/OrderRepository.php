<?php

namespace App\Http\Services\Tenant\Orders;

use App\Models\Tenant\Orders\Order;
use App\Models\Tenant\Orders\OrderDish;
use App\Models\Tenant\Orders\OrderProduct;
use Illuminate\Support\Facades\DB;

class OrderRepository
{
    public function store(array $dto): Order
    {
        return Order::create($dto);
    }

    public function update(int $id, array $dto): Order
    {
        $order  =   Order::findOrFail($id);
        $order->update($dto);
        return $order;
    }

    public function storeOrderProduct(array $dto): void
    {
        OrderProduct::insert($dto);
    }

    public function storeOrderDish(array $dto): void
    {
        OrderDish::insert($dto);
    }

    public function getOrderTable(int $table_id)
    {
        $item   =   DB::table('orders as o')
            ->join('reservations as r', 'r.order_id', 'o.id')
            ->join('tables as t', 't.id', 'r.table_id')
            ->where('o.table_id', $table_id)
            ->where('r.status', 'OCUPADO')
            ->select(
                'o.id as order_id',
                'o.code as order_code',
                'o.customer_name',
                'o.customer_type_document_abbreviation',
                'o.customer_document_number',
                'o.creator_user_name',
                'o.created_at',
                'o.status',
                'o.total',
                'o.subtotal',
                'o.igv',
                'o.observation',
                't.name as table_name'
            )->first();

        return $item;
    }

    public function findOrder(int $id)
    {
        return Order::findOrFail($id);
    }

    public function getOrderProducts(int $id)
    {
        $order_products =   OrderProduct::where('order_id', $id)->where('status','<>','ANULADO')->where('delete_status',false)->get();
        return $order_products;
    }

    public function getOrderDishes(int $id)
    {
        $order_dishes =   OrderDish::where('order_id', $id)->where('status','<>','ANULADO')->where('delete_status',false)->get();
        return $order_dishes;
    }

    public function deleteOrderProducts(int $order_id)
    {
        OrderProduct::where('order_id', $order_id)->delete();
    }

    public function deleteOrderDishes(int $order_id)
    {
        OrderDish::where('order_id', $order_id)->delete();
    }

    public function cancelOrderProducts(int $order_id)
    {
        OrderProduct::where('order_id', $order_id)->update([
            'status' => 'ANULADO',
            'delete_status' => true,
        ]);
    }

    public function cancelOrderDishes(int $order_id)
    {
        OrderDish::where('order_id', $order_id)->update([
            'status' => 'ANULADO',
            'delete_status' => true,
        ]);
    }
}
