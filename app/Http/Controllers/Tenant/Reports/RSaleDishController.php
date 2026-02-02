<?php

namespace App\Http\Controllers\Tenant\Reports;

use App\Exports\Tenant\Reports\RSaleDish\RSaleDishExport;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Tenant\Supply\Dish\Dish;
use App\Models\Tenant\Supply\TypeDish\TypeDish;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class RSaleDishController extends Controller
{
    public function index()
    {
        return view('reports.sales_dishes.index');
    }

    public function getAll(Request $request)
    {
        $items          =   $this->queryAll($request);
        $utility_total  =   $this->utilityTotal($request);

        return DataTables::of($items)
            ->with([
                'utility_total' => $utility_total
            ])->make(true);
    }

    public function queryAll(Request $request)
    {
        $dish_id        =   $request->get('dish_id');
        $start_date     =   $request->get('start_date');
        $end_date       =   $request->get('end_date');

        $q1 = DB::table('sales_dishes as sd')
            ->join('dishes as d', 'd.id', '=', 'sd.dish_id')
            ->select(
                'sd.dish_id',
                'sd.dish_name',
                'sd.sale_price',
                'd.purchase_price',
                DB::raw('SUM(sd.quantity) as quantity'),
                DB::raw('SUM(sd.total) as total'),
                DB::raw('SUM(sd.quantity * d.purchase_price) as purchase_total'),
                DB::raw('SUM(sd.total - (sd.quantity * d.purchase_price)) as utility')
            )
            ->orderBy('sd.dish_name')
            ->groupBy(
                'sd.dish_id',
                'sd.dish_name',
                'd.purchase_price',
                'sd.sale_price'
            );


        if ($start_date) {
            $q1->whereDate('sd.created_at', '>=', $start_date);
        }
        if ($end_date) {
            $q1->whereDate('sd.created_at', '<=', $end_date);
        }

        if ($dish_id) {
            $q1->where('sd.dish_id', $dish_id);
        }

        return $q1;
    }

    public function utilityTotal(Request $request)
    {
        $q = DB::table('sales_dishes as sd')
            ->join('dishes as d', 'd.id', '=', 'sd.dish_id')
            ->select(
                DB::raw('SUM(sd.total - (sd.quantity * d.purchase_price)) as utility_total')
            );

        if ($request->start_date) {
            $q->whereDate('sd.created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $q->whereDate('sd.created_at', '<=', $request->end_date);
        }

        if ($request->dish_id) {
            $q->where('sd.dish_id', $request->dish_id);
        }

        return $q->value('utility_total') ?? 0;
    }

    public function excel(Request $request)
    {

        $company        =   Company::findOrFail(1);
        $data           =   $this->queryAll($request)->get();
        $utility_total  =   $this->utilityTotal($request);

        if ($request->get('dish_id')) {
            $dish       =   Dish::findOrFail($request->get('dish_id'));
            $category   =   TypeDish::findOrFail($dish->type_dish_id);
            $request->merge([
                'dish_name' => $dish->name,
                'type_dish_name' => $category->name,
            ]);
        }

        $request->merge(['utility_total' => $utility_total]);

        return Excel::download(new RSaleDishExport($data, $request, $company), 'r_venta_plato_' . Carbon::now() . '.xlsx');
    }

    public function pdf(Request $request)
    {

        $company        =   Company::find(1);
        $data           =   $this->queryAll($request)->get();
        $utility_total  =   $this->utilityTotal($request);

        if ($request->get('dish_id')) {
            $dish       =   Dish::findOrFail($request->get('dish_id'));
            $type_dish  =   TypeDish::findOrFail($dish->type_dish_id);
            $request->merge([
                'dish_name' => $dish->name,
                'type_dish_name' => $type_dish->name,
            ]);
        }

        $request->merge(['utility_total' => $utility_total]);

        $pdf = Pdf::loadview('reports.sales_dishes.pdf.pdf', [
            'company'   => $company,
            'data'      => $data,
            'filters'   => $request,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('r_ventas_platos' . Carbon::now()->format('Y_m_d_H_i_s') . '.pdf');
    }
}
