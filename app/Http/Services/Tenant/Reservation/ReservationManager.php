<?php

namespace App\Http\Services\Tenant\Reservation;

use Illuminate\Contracts\View\View;

class ReservationManager
{
    private ReservationService $s_service;

     public function __construct()
    {
        $this->s_service    =   new ReservationService();
    }


}
