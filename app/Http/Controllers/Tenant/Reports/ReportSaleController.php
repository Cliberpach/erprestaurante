<?php

namespace App\Http\Controllers\Tenant\Reports;

use App\Exports\Tenant\Reports\RSale\RSaleExport;
use App\Http\Controllers\Controller;
use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ReportSaleController extends Controller
{
    public function index()
    {
        return view('reports.report_sales.index');
    }

    public function getReporteVenta(Request $request)
    {

        $items  =   $this->queryReporteVenta($request);
        $total  =   $this->total($items);

        return DataTables::of($items)
            ->filterColumn('item_name', function ($query, $keyword) {
                $query->where('rs.item_name', 'LIKE', "%{$keyword}%");
            })
            ->with([
                'total' => $total
            ])->make(true);
    }

    public function queryReporteVenta(Request $request)
    {
        $filter_product =   $request->get('product_id');
        $filter_dish    =   $request->get('dish_id');
        $date_start     =   $request->get('date_start');
        $date_end       =   $request->get('date_end');

        $q1    =   DB::table('sales_products as sp')
            ->join('sales as s', 's.id', 'sp.sale_id')
            ->select(
                'sp.sale_id',
                DB::raw("CONCAT(s.serie, '-', s.correlative) as document"),
                'sp.product_name as item_name',
                'sp.quantity',
                'sp.sale_price',
                'sp.total',
                's.created_at',
                'sp.category_name',
                'sp.brand_name'
            )
            ->orderByDesc('s.created_at');

        if ($filter_product) {
            $q1->where('sp.product_id', $filter_product);
        }

        $q2    =   DB::table('sales_dishes as sd')
            ->join('sales as s', 's.id', 'sd.sale_id')
            ->select(
                'sd.sale_id',
                DB::raw("CONCAT(s.serie, '-', s.correlative) as document"),
                'sd.dish_name as item_name',
                'sd.quantity',
                'sd.sale_price',
                'sd.total',
                's.created_at'
            )
            ->orderByDesc('s.created_at');

        if ($filter_dish) {
            $q2->where('sd.dish_id', $filter_dish);
        }

        $union    =   $q1->unionAll($q2);
        $report_sale = DB::query()
            ->fromSub($union, 'rs')
            ->orderByDesc('rs.created_at')
            ->orderBy('rs.document', 'asc')
            ->orderBy('rs.item_name', 'asc');

        if ($date_start) {
            $report_sale = $report_sale->whereRaw('DATE(rs.created_at) >= ?', [$date_start]);
        }

        if ($date_end) {
            $report_sale = $report_sale->whereRaw('DATE(rs.created_at) <= ?', [$date_end]);
        }

        return $report_sale;
    }

    public function total($items)
    {
        return $items->sum('rs.total');
    }

    public function excel(Request $request)
    {
        $company        =   Company::find(1);
        $report_sale    =   $this->queryReporteVenta($request);
        $total          =   $this->total($report_sale);
        $request->merge(['total' => $total]);

        return Excel::download(
            new RSaleExport($report_sale->get(), $request, $company),
            'reporte_ventas_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx'
        );
    }

    public function pdf(Request $request)
    {
        $company        =   Company::find(1);
        $report_sale    =   $this->queryReporteVenta($request);
        $total          =   $this->total($report_sale);
        $request->merge(['total' => $total]);

        $pdf = Pdf::loadview('reports.report_sales.pdf.pdf', [
            'company'               =>  $company,
            'report_sale'           =>  $report_sale->get(),
            'filters'               =>  $request

        ])->setPaper('a4', 'portrait');


        return $pdf->stream('reporte_ventas_' . Carbon::now()->format('Y_m_d_H_i_s') . '.pdf');
    }
}
