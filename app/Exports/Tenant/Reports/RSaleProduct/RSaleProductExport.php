<?php

namespace App\Exports\Tenant\Reports\RSaleProduct;


use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RSaleProductExport  implements FromView
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
        return view('reports.sales_products.excel.excel', [
            'data'                      =>  $this->data,
            'filters'                   =>  $this->filters,
            'company'                   =>  $this->company
        ]);
    }
}
