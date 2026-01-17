<?php

namespace App\Http\Services\Tenant\Reservation;

use App\Http\Controllers\FormatController;
use App\Http\Controllers\UtilController;
use App\Http\Services\Tenant\Cash\PettyCashBook\PettyCashBookService;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Company;
use App\Models\Tenant\Reservation\Reservation;
use App\Models\Tenant\Supply\Programming\ProgrammingDetail;
use App\Models\Tenant\Supply\Table\Table;
use App\Models\Tenant\WarehouseProduct;
use Exception;
use Illuminate\Support\Facades\Auth;

class ReservationValidation
{
    private PettyCashBookService $s_cash_book;

    public function __construct()
    {
        $this->s_cash_book = new PettyCashBookService();
    }

  
}
