<?php

namespace App\Http\Services\Tenant\Reservation;

use App\Models\Tenant\Reservation\Reservation;

class ReservationRepository
{
    public function store(array $dto): Reservation
    {
        return Reservation::create($dto);
    }

    public function setStatusByOrder(int $order_id, string $status)
    {
        $item           =   Reservation::where('order_id', $order_id)->first();
        $item->status   =   $status;
        $item->save();
    }

    public function changeTable(int $order_id, int $table_selected)
    {
        $reservation            =   Reservation::where('order_id', $order_id)->first();
        $reservation->table_id  =   $table_selected;
        $reservation->save();
    }

    public function deleteFromOrder(int $order_id)
    {
        $item           =   Reservation::where('order_id', $order_id)->first();
        $item->status   =   'ANULADO';
        $item->save();
    }
}
