<?php

namespace App\Http\Controllers\Tenant\Reports;

use App\Exports\Tenant\Reports\RSaleProduct\RSaleProductExport;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class RSaleProductController extends Controller
{
    public function index()
    {
        return view('reports.sales_products.index');
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
        $product_id     =   $request->get('product_id');
        $start_date     =   $request->get('start_date');
        $end_date       =   $request->get('end_date');

        $q1 = DB::table('sales_products as sp')
            ->join('products as p', 'p.id', '=', 'sp.product_id')
            ->select(
                'sp.product_id',
                'sp.product_name',
                'sp.category_name',
                'sp.brand_name',
                'sp.sale_price',
                'p.purchase_price',
                DB::raw('SUM(sp.quantity) as quantity'),
                DB::raw('SUM(sp.total) as total'),
                DB::raw('SUM(sp.quantity * p.purchase_price) as purchase_total'),
                DB::raw('SUM(sp.total - (sp.quantity * p.purchase_price)) as utility')
            )
            ->orderBy('sp.product_name')
            ->groupBy(
                'sp.product_id',
                'sp.product_name',
                'sp.category_name',
                'sp.brand_name',
                'p.purchase_price',
                'sp.sale_price'
            );


        if ($start_date) {
            $q1->whereDate('sp.created_at', '>=', $start_date);
        }
        if ($end_date) {
            $q1->whereDate('sp.created_at', '<=', $end_date);
        }

        if ($product_id) {
            $q1->where('sp.product_id', $product_id);
        }

        return $q1;
    }

    public function utilityTotal(Request $request)
    {
        $q = DB::table('sales_products as sp')
            ->join('products as p', 'p.id', '=', 'sp.product_id')
            ->select(
                DB::raw('SUM(sp.total - (sp.quantity * p.purchase_price)) as utility_total')
            );

        if ($request->start_date) {
            $q->whereDate('sp.created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $q->whereDate('sp.created_at', '<=', $request->end_date);
        }

        if ($request->product_id) {
            $q->where('sp.product_id', $request->product_id);
        }

        return $q->value('utility_total') ?? 0;
    }

    public function excel(Request $request)
    {

        $company        =   Company::findOrFail(1);
        $data           =   $this->queryAll($request)->get();
        $utility_total  =   $this->utilityTotal($request);

        if ($request->get('product_id')) {
            $product    =   Product::findOrFail($request->get('product_id'));
            $category   =   Category::findOrFail($product->category_id);
            $brand      =   Brand::findOrFail($product->brand_id);
            $request->merge([
                'product_name' => $product->name,
                'category_name' => $category->name,
                'brand_name' => $brand->name,
            ]);
        }

        $request->merge(['utility_total' => $utility_total]);

        return Excel::download(new RSaleProductExport($data, $request, $company), 'r_venta_producto_' . Carbon::now() . '.xlsx');
    }

    public function pdf(Request $request)
    {

        $company    =   Company::find(1);
        $data           =   $this->queryAll($request)->get();
        $utility_total  =   $this->utilityTotal($request);

        if ($request->get('product_id')) {
            $product    =   Product::findOrFail($request->get('product_id'));
            $category   =   Category::findOrFail($product->category_id);
            $brand      =   Brand::findOrFail($product->brand_id);
            $request->merge([
                'product_name' => $product->name,
                'category_name' => $category->name,
                'brand_name' => $brand->name,
            ]);
        }

        $request->merge(['utility_total' => $utility_total]);

        $pdf = Pdf::loadview('reports.sales_products.pdf.pdf', [
            'company'   => $company,
            'data'      => $data,
            'filters'   => $request,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('r_ventas_productos' . Carbon::now()->format('Y_m_d_H_i_s') . '.pdf');
    }
}
