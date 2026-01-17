<?php

namespace App\Http\Services\Tenant\Reservation;

use App\Models\Tenant\Orders\Order;
use App\Models\Tenant\Orders\OrderDish;
use App\Models\Tenant\Orders\OrderProduct;
use App\Models\Tenant\Reservation\Reservation;

class ReservationRepository
{
    public function store(array $dto): Reservation
    {
        return Reservation::create($dto);
    }

    public function storeOrderProduct(array $dto): void
    {
        OrderProduct::insert($dto);
    }

    public function storeOrderDish(array $dto): void
    {
        OrderDish::insert($dto);
    }
}
