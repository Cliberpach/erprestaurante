<?php

namespace App\Http\Services\Tenant\Reservation;

use App\Models\Company;
use App\Models\Landlord\Customer;
use App\Models\Product;
use App\Models\Tenant\Orders\Order;
use App\Models\Tenant\Supply\Dish\Dish;

class ReservationDto
{
    public function getDtoStore(Order $order):array{
        $dto    =   [];

        $dto['table_id']    =   $order->table_id;
        $dto['order_id']    =   $order->id;
        $dto['customer_id'] =   $order->customer_id;
        $dto['date']        =   $order->date;

        return $dto;
    }
}
