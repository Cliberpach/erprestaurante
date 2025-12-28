<?php

namespace App\Http\Services\Tenant\Orders;

use App\Http\Controllers\UtilController;
use App\Http\Services\Tenant\Cash\PettyCashBook\PettyCashBookService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    private OrderValidation $s_validation;

    public function __construct(){
        $this->s_validation =   new OrderValidation();
    }

    public function create(int $table_id): View
    {
        $vars   =   $this->s_validation->validationCreate($table_id);

        return view('orders.create', $vars);
    }
}
