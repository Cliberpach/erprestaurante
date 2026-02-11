<?php

namespace App\Http\Controllers\Tenant\Sales;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Sale\QuerySale\QuerySaleRequest;
use App\Http\Services\Tenant\Sale\Sale\SaleManager;
use App\Models\Tenant\Sales\Sale\Sale;
use App\Models\Ventas\Venta;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class QuerySaleController extends Controller
{
    protected SaleManager $s_manager;

    public function __construct()
    {
        $this->s_manager  =   new SaleManager();
    }

    public function index()
    {
        return view('sales.sale_document.consultar.index');
    }

    /*
array:6 [ // app\Http\Controllers\Market\Ventas\ConsultarComprobanteController.php:15
  "tipo_doc"        => "01"
  "fecha_emision"   => "2025-02-22"
  "serie"           => "B001"
  "correlativo"     => "123"
  "doc_cliente"     => "33321312321"
  "monto_total"     => "22"
]
*/
    public function consultarComprobante(QuerySaleRequest $request)
    {
        try {

            $tipo_doc       = $request->tipo_doc;
            $fecha_emision  = $request->fecha_emision;
            $serie          = $request->serie;
            $correlativo    = $request->correlativo;
            $doc_cliente    = $request->doc_cliente;
            $monto_total    = $request->monto_total;

            $documento  =   null;
            if ($tipo_doc != '07') {

                $documento  =   DB::table('sales as s')
                    ->select(
                        's.customer_document_number AS cliente_numero_documento',
                        's.serie',
                        's.correlative AS correlativo',
                        's.total',
                        's.type_sale_code AS tipo_venta_codigo',
                        's.created_at'
                    )
                    ->where('s.type_sale_code', $tipo_doc)
                    ->whereDate('s.created_at', $fecha_emision)
                    ->where('s.serie', $serie)
                    ->where('s.correlative', $correlativo)
                    ->where('s.customer_document_number', $doc_cliente)
                    ->whereRaw('TRUNCATE(s.total, 2) = ?', [$monto_total]) //======== RECORTAR VALOR - SIN REDONDEAR =====
                    ->get();
            } else {
                $documento  =   DB::table('credit_notes as nc')
                    ->select('nc.*')
                    ->whereDate('nc.created_at', $fecha_emision)
                    ->where('nc.serie', $serie)
                    ->where('nc.correlative AS correlativo', $correlativo)
                    ->where('nc.customer_document_number AS cliente_numero_documento', $doc_cliente)
                    ->whereRaw('TRUNCATE(nc.total, 2) = ?', [$monto_total]) //======== RECORTAR VALOR - SIN REDONDEAR =====
                    ->get();
            }

            $cant   =   count($documento);

            if ($cant > 1) {
                throw new Exception("ERROR EN LA CONSULTA :C");
            }
            if ($cant === 1) {
                $documento  =   $documento[0];
            }
            if ($cant === 0) {
                $documento  =   null;
            }


            return response()->json(['success' => true, 'message' => 'CONSULTA COMPLETADA', 'documento' => $documento]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function validarExistencia(Request $request)
    {

        //============ VALIDAR EXISTENCIA DEL COMPROBANTE =========
        $tipo_doc       = $request->tipo_doc;
        $fecha_emision  = $request->fecha_emision;
        $serie          = $request->serie;
        $correlativo    = $request->correlativo;
        $doc_cliente    = $request->doc_cliente;
        $monto_total    = $request->monto_total;


        $documento  =   [];
        if ($tipo_doc != '07') {

            $documento  =   DB::table('sales as s')
                ->select('s.*')
                ->where('s.type_sale_code', $tipo_doc)
                ->whereDate('s.created_at', $fecha_emision)
                ->where('s.serie', $serie)
                ->where('s.correlative', $correlativo)
                ->where('s.customer_document_number', $doc_cliente)
                ->whereRaw('TRUNCATE(s.total, 2) = ?', [$monto_total]) //======== RECORTAR VALOR - SIN REDONDEAR =====
                ->get();
        } else {

            $documento  =   DB::table('credit_notes as nc')
                ->select('nc.*')
                ->whereDate('nc.created_at', $fecha_emision)
                ->where('nc.serie', $serie)
                ->where('nc.correlative', $correlativo)
                ->where('nc.customer_document_number', $doc_cliente)
                ->whereRaw('TRUNCATE(nc.total, 2) = ?', [$monto_total]) //======== RECORTAR VALOR - SIN REDONDEAR =====
                ->get();
        }

        if (count($documento) === 0) {
            dd('DOCUMENTO NO ENCONTRADO');
        }

        return $documento[0];
    }

    /*
array:7 [▼ // app\Http\Controllers\Market\Ventas\ConsultarComprobanteController.php:82
  "tipo_doc"            => "01"
  "fecha_emision"       => "2025-02-21 16:36:07"
  "serie"               => "B001"
  "correlativo"         => "1"
  "doc_cliente"         => "99999999"
  "monto_total"         => "2.000000"
]
*/
    public function pdf(Request $request)
    {

        //============ VALIDAR EXISTENCIA DEL COMPROBANTE =========
        $documento              =   $this->validarExistencia($request);
        $sale_id                =   $documento->id;
        $sale                   =   Sale::findOrFail($sale_id);
        $res                    =   $this->s_manager->pdf_voucher($sale_id, 0);
        return $res['pdf']->stream($res['pdf_name'] . '.pdf');
    }


    /*
array:7 [▼ // app\Http\Controllers\Market\Ventas\ConsultarComprobanteController.php:82
  "tipo_doc"            => "01"
  "fecha_emision"       => "2025-02-21 16:36:07"
  "serie"               => "B001"
  "correlativo"         => "1"
  "doc_cliente"         => "99999999"
  "monto_total"         => "2.000000"
]
*/
    public function xml(Request $request)
    {

        //============ VALIDAR EXISTENCIA DEL COMPROBANTE =========
        $documento              =   $this->validarExistencia($request);

        $sale_document  =   Sale::findOrFail($documento->id);


        $ruta_xml       =   $sale_document->ruta_xml;

        if (!$ruta_xml) {
            abort(404, 'Archivo no encontrado');
        }

        $filePath       = public_path("{$ruta_xml}");


        if (File::exists($filePath)) {
            return response()->download($filePath);
        } else {
            abort(404, 'Archivo no encontrado');
        }
    }


    /*
    array:7 [▼ // app\Http\Controllers\Market\Ventas\ConsultarComprobanteController.php:82
    "tipo_doc"            => "01"
    "fecha_emision"       => "2025-02-21 16:36:07"
    "serie"               => "B001"
    "correlativo"         => "1"
    "doc_cliente"         => "99999999"
    "monto_total"         => "2.000000"
    ]
*/
    public function cdr(Request $request)
    {

        //============ VALIDAR EXISTENCIA DEL COMPROBANTE =========
        $documento              =   $this->validarExistencia($request);

        $sale_document  =   Sale::findOrFail($documento->id);


        $ruta_cdr       =   $sale_document->ruta_cdr;

        if (!$ruta_cdr) {
            abort(404, 'Archivo no encontrado');
        }

        $filePath       = public_path("{$ruta_cdr}");


        if (File::exists($filePath)) {
            return response()->download($filePath);
        } else {
            abort(404, 'Archivo no encontrado');
        }
    }
}
