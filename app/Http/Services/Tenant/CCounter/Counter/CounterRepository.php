<?php

namespace App\Http\Services\Tenant\CCounter\Counter;

use App\Models\Tenant\Reservation\Reservation;

class CounterRepository
{
    public function getOrder(int $order)
    {
        $order  =    Reservation::from('reservations as r')
                    ->join('orders as o', 'o.id', 'r.order_id')
                    ->join('tables as t', 't.id', 'o.table_id')
                    ->where('r.status', 'OCUPADO')
                    ->where('o.id',$order)
                    ->select(
                        'o.id as order_id',
                        't.name as table_name',
                        'o.code as order_code',
                        'o.created_at',
                        'o.creator_user_name',
                        'o.customer_name',
                        'r.status',
                        'o.total',
                        'o.subtotal',
                        'o.igv'
                    )->first();

        return $order;
    }
}
