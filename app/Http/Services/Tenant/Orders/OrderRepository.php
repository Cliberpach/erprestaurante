<?php

namespace App\Http\Services\Tenant\Orders;

use App\Models\Tenant\Orders\Order;
use App\Models\Tenant\Orders\OrderDish;
use App\Models\Tenant\Orders\OrderProduct;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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

    public function findOrder(int $id): Order
    {
        return Order::findOrFail($id);
    }

    public function getOrderProducts(int $id)
    {
        $order_products =   OrderProduct::where('order_id', $id)->where('status', '!=', 'ANULADO')->where('delete_status', false)->get();
        return $order_products;
    }

    public function getOrderDishes(int $id)
    {
        $order_dishes =   OrderDish::where('order_id', $id)->where('status', '<>', 'ANULADO')->where('delete_status', false)->get();
        return $order_dishes;
    }

    public function getOrderProductsCanceled(int $id)
    {
        $order_products =   OrderProduct::where('order_id', $id)->where('status', 'ANULADO')->where('delete_status', true)->get();
        return $order_products;
    }

    public function getOrderDishesCanceled(int $id)
    {
        $order_dishes =   OrderDish::where('order_id', $id)->where('status', 'ANULADO')->where('delete_status', true)->get();
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

    public function setStatusInvoice(int $id, string $status, $invoice)
    {
        $order                      =   Order::findOrFail($id);
        $order->status_invoice      =   $status;
        $order->status              =   'FINALIZADO';
        $order->sale_id             =   $invoice->id;
        $order->sale_serie          =   $invoice->serie;
        $order->sale_correlative    =   $invoice->correlative;
        $order->save();
    }

    public function getDetails(int $id)
    {
        $q1 = DB::connection('tenant')
            ->table('orders_products as op')
            ->select(
                'op.id',
                'op.order_id',
                DB::raw("'PRODUCTO' as item_type"),
                'op.product_id as item_id',
                'op.product_name as item_name',
                'op.quantity',
                'op.sale_price',
                'op.total',
                'op.detail_printed',
                'op.observation',
                'op.created_at'
            )
            ->where('op.order_id', $id)
            ->where('op.status', '<>', 'ANULADO');

        $q2 = DB::connection('tenant')
            ->table('orders_dishes as od')
            ->select(
                'od.id',
                'od.order_id',
                DB::raw("'PLATO' as item_type"),
                'od.dish_id as item_id',
                'od.dish_name as item_name',
                'od.quantity',
                'od.sale_price',
                'od.total',
                'od.detail_printed',
                'od.observation',
                'od.created_at'
            )
            ->where('od.order_id', $id)
            ->where('od.status', '<>', 'ANULADO');

        return $q1->unionAll($q2)->get();
    }

    public function cancelOrderDetailsByIds(array $dishIds = [], array $productIds = []): void
    {
        // ===== PLATOS =====
        if (!empty($dishIds)) {
            OrderDish::whereIn('id', $dishIds)
                ->update([
                    'status' => 'ANULADO',
                    'updated_at' => now(),
                    'cancellation_date' =>  now(),
                    'delete_status' => true
                ]);
        }

        // ===== PRODUCTOS =====
        if (!empty($productIds)) {
            OrderProduct::whereIn('id', $productIds)
                ->update([
                    'status' => 'ANULADO',
                    'updated_at' => now(),
                    'cancellation_date' =>  now(),
                    'delete_status' => true
                ]);
        }
    }

    public function updateOrderDish(array $dto_odish_olds): void
    {
        foreach ($dto_odish_olds as $dish) {
            DB::table('orders_dishes')
                ->where('id', $dish['order_detail_id'])
                ->update([
                    'quantity'       => $dish['quantity'],
                    'observation'    => $dish['observation'] ?? null,
                    'sale_price'     => $dish['sale_price'] ?? null,
                    'updated_at'     => now(),
                ]);
        }
    }

    public function updateOrderProduct(array $dto_oproduct_olds): void
    {
        foreach ($dto_oproduct_olds as $item) {
            DB::table('orders_products')
                ->where('id', $item['order_detail_id'])
                ->update([
                    'quantity'       => $item['quantity'],
                    'observation'    => $item['observation'],
                    'purchase_price' => $item['purchase_price'],
                    'sale_price'     => $item['sale_price'],
                    'updated_at'     => now(),
                ]);
        }
    }

    public function setPendingPrint(int $id, string $status): Order
    {
        $order                  =   Order::findOrFail($id);
        $order->pending_print   =   $status;
        $order->save();
        return $order;
    }

    public function changeTable(int $order_id, int $table_selected): Order
    {
        $order                      =   $this->findOrder($order_id);
        $order->table_id            =   $table_selected;
        $order->date_change_table   =   now();
        $order->save();
        return $order;
    }

    public function deleteOrder(int $id)
    {
        $order  =   Order::findOrFail($id);
        $order->status  =   'ANULADO';
        $order->save();

        $this->cancelOrderDishes($id);
        $this->cancelOrderProducts($id);
    }

    public function auditPayRef(Order $order)
    {
        $user                       =   Auth::user();
        $order->payref_user_id      =   $user->id;
        $order->payref_user_name    =   $user->name;
        $order->payref_date         =   Carbon::now();
        $order->save();
    }
}
