<?php

namespace App\Http\Services\Tenant\CCounter\Counter;

use App\Models\Tenant\Reservation\Reservation;
use Illuminate\Support\Facades\DB;

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
                        'o.status as order_status',
                        'o.status_invoice',

                        'o.total',
                        'o.subtotal',
                        'o.igv',

                        'o.sale_serie',
                        'o.sale_correlative',
                        'o.payref_img_url',
                        'o.customer_id',
                        'o.customer_type_document_abbreviation',
                        'o.customer_document_number'
                    )->first();

        return $order;
    }

    public function getReservationStatusByOrder(int $order_id): ?string
    {
        return DB::table('reservations')
            ->where('order_id', $order_id)
            ->value('status');
    }

    public function getWaitersByCashier(int $cashier_id)
    {
        return DB::table('petty_cash_books as pcb')
            ->join('petty_cash_servers as pcs', 'pcs.petty_cash_book_id', '=', 'pcb.id')
            ->join('users as u', 'u.id', '=', 'pcs.user_id')
            ->where('pcb.user_id', $cashier_id)
            ->where('pcb.status', 'ABIERTO')
            ->whereNull('pcb.final_date')
            ->select('u.id', 'u.name')
            ->get();
    }

    public function changeWaiter(int $order_id, int $waiter_id): void
    {
        $waiter_name = DB::table('users')->where('id', $waiter_id)->value('name');

        DB::table('orders')
            ->where('id', $order_id)
            ->update([
                'creator_user_id'   => $waiter_id,
                'creator_user_name' => $waiter_name,
            ]);
    }
}
