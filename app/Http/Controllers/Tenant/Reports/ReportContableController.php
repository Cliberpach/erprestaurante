<?php

namespace App\Http\Controllers\Tenant\Reports;

use App\Exports\Tenant\ReportContableExport;
use App\Exports\Tenant\Reports\RContable\RContableExport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilController;
use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ReportContableController extends Controller
{
    public function index()
    {

        $invoices   =   UtilController::getInvoiceTypes()->where('id', '<>', 67);
        return view('reports.report_contable.index', compact('invoices'));
    }

    /*
"date_start"        => null
"date_end"          => null
"filter_document"   => null
*/
    public function getAll(Request $request)
    {

        $items =   $this->queryRContable($request);

        return DataTables::of($items)->make(true);
    }

    public function queryRContable(Request $request)
    {

        $items    =   [];
        if ($request->get('filter_document') && in_array($request->get('filter_document'), [65, 66, 67])) {
            $items  =   $this->querySales($request->get('filter_document'));
        }

        if ($request->get('filter_document') == 'VENTAS') {
            $items  =   $this->querySales();
        }

        if ($request->get('date_start')) {
            $items = $items->whereRaw('DATE(sdd.created_at) >= ?', [$request->get('date_start')]);
        }

        if ($request->get('date_end')) {
            $items = $items->whereRaw('DATE(sdd.created_at) <= ?', [$request->get('date_end')]);
        }

        return $items;
    }

    public function querySales($filter_document = null)
    {
        $items    =   DB::table('sales as s')
            ->select(
                's.created_at',
                's.type_sale_name',
                's.type_sale_code',
                's.serie',
                's.correlative',
                's.creator_user_name',
                's.customer_type_document',
                's.customer_document_code',
                's.customer_document_number',
                's.customer_name',
                DB::raw("FORMAT(s.subtotal, 2) as subtotal"),
                DB::raw("FORMAT(s.igv_amount, 2) as igv_amount"),
                DB::raw("FORMAT(s.igv_percentage, 2) as igv_percentage"),
                DB::raw("FORMAT(s.total, 2) as total"),
                's.sunat_status',
            )->where('type_sale_id', '<>', 67)
            ->orderByDesc('s.created_at')
            ->orderBy('s.serie')
            ->orderBy('s.correlative');

        if ($filter_document) {
            $items->where('s.type_sale_id', $filter_document);
        }

        return $items;
    }

    public function excel(Request $request)
    {
        $company            =   Company::findOrFail(1);
        $report_contable    =   $this->queryRContable($request)->get();

        return Excel::download(
            new RContableExport($report_contable, $request, $company),
            'reporte_contable_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx'
        );
    }

    public function pdf(Request $request)
    {

        $company            =   Company::find(1);
        $report_contable    =   $this->queryRContable($request)->get();

        $pdf = Pdf::loadview('reports.report_contable.pdf.pdf', [
            'company'               =>  $company,
            'report_contable'       =>  $report_contable,
            'filters'               =>  $request

        ])->setPaper('a4', 'landscape');


        return $pdf->stream('reporte_campos_' . Carbon::now()->format('Y_m_d_H_i_s') . '.pdf');
    }
}
