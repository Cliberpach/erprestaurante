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
}
