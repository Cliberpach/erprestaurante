<?php

namespace App\Http\Controllers\Tenant\Sales;

use App\Http\Controllers\Controller;
use App\Http\Services\Tenant\CreditNote\CreditNoteManager;
use App\Models\Tenant\Sales\CreditNote\CreditNote;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\File;

class CreditNoteController extends Controller
{
    private CreditNoteManager $s_manager;

    public function __construct()
    {
        $this->s_manager    =   new CreditNoteManager();
    }

    public function index()
    {
        return view('sales.credit_notes.index');
    }

    public function getAll(Request $request)
    {
        $filter_customer    =   $request->get('customer_id');
        $filter_start_date  =   $request->get('start_date');
        $filter_end_date    =   $request->get('end_date');
        $filter_sunat       =   $request->get('status');

        $sales    =   DB::table('credit_notes as cn')
            ->select(
                'cn.id',
                'cn.created_at',
                'cn.customer_id',
                'cn.customer_name',
                DB::raw('CONCAT(cn.customer_type_document,":",cn.customer_document_number,"-",cn.customer_name) as customer_full_name'),
                'cn.serie',
                'cn.correlative',
                DB::raw("CONCAT(cn.serie, '-', cn.correlative) AS doc"),
                'cn.type_sale_name',
                'cn.type_sale_code',
                DB::raw("FORMAT(cn.igv_percentage, 2) AS igv_percentage"),
                DB::raw("FORMAT(cn.subtotal, 2) AS subtotal"),
                DB::raw("FORMAT(cn.igv_amount, 2) AS igv_amount"),
                DB::raw("FORMAT(cn.total, 2) AS total"),
                'cn.type_sale_code',
                'cn.ruta_xml',
                'cn.ruta_cdr',
                'cn.sunat_status',
                'cn.petty_cash_book_id',
                'cn.petty_cash_name'
            );

        if ($filter_customer) {
            $sales->where('cn.customer_id', $filter_customer);
        }
        if ($filter_start_date) {
            $sales->whereDate('cn.created_at', '>=', $filter_start_date);
        }
        if ($filter_end_date) {
            $sales->whereDate('cn.created_at', '<=', $filter_end_date);
        }
        if ($filter_sunat) {
            $sales->where('cn.sunat_status', $filter_sunat);
        }

        return DataTables::of($sales)
            ->filterColumn('customer_full_name', function ($query, $keyword) {
                $query->whereRaw("
                    CONCAT(
                            cn.customer_type_document, ':',
                            cn.customer_document_number, '-',
                            cn.customer_name
                        ) LIKE ?
                    ", ["%{$keyword}%"]);
            })
            ->filterColumn('doc', function ($query, $keyword) {
                $query->whereRaw("
                    CONCAT(
                            cn.serie,'-',
                            cn.correlative
                        ) LIKE ?
                    ", ["%{$keyword}%"]);
            })
            ->make(true);
    }

    /*
array:1 [ // app\Http\Controllers\Tenant\Sales\CreditNoteController.php:83
  "credit_note_id" => "12"
]
*/
    public function sendSunat(Request $request)
    {
        try {
            $credit_note_id   =   $request->get('credit_note_id');

            if (!$credit_note_id) {
                throw new Exception("NO SE ENCONTRÓ EL ID DE LA NOTA DE CRÉDITO");
            }

            $res            =   $this->s_manager->sendSunat($credit_note_id);

            return response()->json(['success' => true, 'message' => $res->last_send_message]);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }

    public function pdfOne(int $id)
    {
        try {

            return $this->s_manager->pdfOne($id);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage(), 'line' => $th->getLine()]);
        }
    }

    public function downloadXml($id)
    {
        $credit_note    =   CreditNote::findOrFail($id);
        $ruta_xml       =   $credit_note->ruta_xml;
        $filePath       =   public_path("{$ruta_xml}");

        if (File::exists($filePath)) {
            return response()->download($filePath);
        } else {
            abort(404, 'Archivo no encontrado');
        }
    }

    public function downloadCdr($sale_document_id)
    {

        $credit_note    =   CreditNote::findOrFail($sale_document_id);
        $ruta_cdr       =   $credit_note->ruta_cdr;
        $filePath       =   public_path("{$ruta_cdr}");

        if (File::exists($filePath)) {
            return response()->download($filePath);
        } else {
            abort(404, 'Archivo no encontrado');
        }
    }
}
