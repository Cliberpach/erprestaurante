<?php

namespace App\Http\Controllers\Tenant\Orders;

use App\Http\Controllers\Controller;
use App\Http\Services\Tenant\Orders\OrderManager;
use Illuminate\Support\Facades\Session;
use Throwable;

class OrderController extends Controller
{
    private OrderManager $s_order;

    public function __construct(){
        $this->s_order  =   new OrderManager();
    }

    public function create(int $table)
    {
        try {
            $view   =   $this->s_order->create($table);
            return $view;
        } catch (Throwable $th) {
            Session::flash('message_error',$th->getMessage());
            return back();
        }
    }
}
