<?php

namespace App\Exports\Tenant\Dashboard;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ConsumableMonthQuantityExport  implements FromView
{
    protected $data;
    protected $filters;
    protected $company;

    public function __construct($data, $filters, $company)
    {
        $this->data     =   $data;
        $this->filters  =   $filters;
        $this->company  =   $company;
    }

    public function view(): View
    {
        return view('dashboard.dashboard.excel.consumables_month_quantity', [
            'data'                      =>  $this->data,
            'filters'                   =>  $this->filters,
            'company'                   =>  $this->company
        ]);
    }
}
