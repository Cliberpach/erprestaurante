<?php

namespace App\Exports\Tenant\Dashboard;


use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProductsMonthExport  implements FromView
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
        return view('dashboard.dashboard.excel.dishes_month', [
            'data'                      =>  $this->data,
            'filters'                   =>  $this->filters,
            'company'                   =>  $this->company
        ]);
    }
}
