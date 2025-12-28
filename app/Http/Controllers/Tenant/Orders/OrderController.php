<?php

namespace App\Http\Controllers\Tenant\Orders;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilController;
use App\Http\Services\Tenant\Orders\OrderManager;
use App\Models\Tenant\Supply\Table\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

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
