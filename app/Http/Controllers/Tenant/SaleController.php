<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FormatController;
use App\Http\Controllers\UtilController;
use App\Http\Requests\Sale\SaleStoreRequest;
use App\Http\Requests\Tenant\Sale\SaleConvertRequest;
use App\Http\Requests\Tenant\Sale\SaleCreditNoteRequest;
use App\Http\Services\Tenant\Sale\Sale\SaleManager;
use App\Models\Company;
use App\Models\CompanyInvoice;
use App\Models\Landlord\Customer;
use App\Models\Tenant\PaymentMethod;
use App\Models\Tenant\Sales\Sale\Sale;
use App\Models\Tenant\Sales\Sale\SaleDish;
use App\Models\Tenant\Sales\Sale\SaleProduct;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\File;
use Throwable;

class SaleController extends Controller
{
    protected SaleManager $s_sale;

    public function __construct()
    {
        $this->s_sale   =   new SaleManager();
    }

    public function index()
    {
        $invoice_types      =   UtilController::getInvoiceTypes()->where('name', '<>', 'NOTA DE VENTA');
        $customer_formatted =   FormatController::getFormatInitialCustomer(1);
        return view('sales.sale_document.index', compact(
            'invoice_types',
            'customer_formatted'
        ));
    }

    public function getSales(Request $request)
    {
        $filter_customer    =   $request->get('customer_id');
        $filter_start_date  =   $request->get('start_date');
        $filter_end_date    =   $request->get('end_date');
        $filter_sunat       =   $request->get('status');

        $sales    =   DB::table('sales as s')
            ->select(
                's.id',
                's.created_at as fecha_registro',
                's.customer_id',
                's.customer_name',
                DB::raw('CONCAT(s.customer_type_document,":",s.customer_document_number,"-",s.customer_name) as customer_full_name'),
                's.serie',
                's.correlative',
                DB::raw("CONCAT(s.serie, '-', s.correlative) AS doc"),
                's.type_sale_name',
                's.type_sale_code',
                DB::raw("FORMAT(s.igv_percentage, 2) AS igv_percentage"),
                DB::raw("FORMAT(s.subtotal, 2) AS subtotal"),
                DB::raw("FORMAT(s.igv_amount, 2) AS igv_amount"),
                DB::raw("FORMAT(s.total, 2) AS total"),
                's.status',
                's.type_sale_code',
                's.ruta_xml',
                's.ruta_cdr',
                's.sunat_status',
                's.converted_to_id',
                's.converted_from_id',
                's.pay_status',
                's.petty_cash_book_id',
                's.petty_cash_name'
            )
            ->where('s.status', '!=', 'ANULADO');

        if ($filter_customer) {
            $sales->where('customer_id', $filter_customer);
        }
        if ($filter_start_date) {
            $sales->whereDate('s.created_at', '>=', $filter_start_date);
        }
        if ($filter_end_date) {
            $sales->whereDate('s.created_at', '<=', $filter_end_date);
        }
        if ($filter_sunat) {
            $sales->where('s.sunat_status', $filter_sunat);
        }

        return DataTables::of($sales)
            ->filterColumn('customer_full_name', function ($query, $keyword) {
                $query->whereRaw("
                    CONCAT(
                            s.customer_type_document, ':',
                            s.customer_document_number, '-',
                            s.customer_name
                        ) LIKE ?
                    ", ["%{$keyword}%"]);
            })
            ->filterColumn('doc', function ($query, $keyword) {
                $query->whereRaw("
                    CONCAT(
                            s.serie,'-',
                            s.correlative
                        ) LIKE ?
                    ", ["%{$keyword}%"]);
            })
            ->make(true);
    }

    public function create()
    {

        $urlImagen = asset('assets/img/products/img_default.png');

        $categories =   DB::select('select * from categories');
        $brands     =   DB::select('select * from brands');
        $customers  =   Customer::where('status', 'ACTIVO')->get();
        $company    =   Company::find(1);

        $types_identity_documents   =   UtilController::getIdentityDocuments();

        $departments    =   DB::select('select * from departments');
        $districts      =   DB::select('select * from districts');
        $provinces      =   DB::select('select * from provinces');

        $company_invoice    =   CompanyInvoice::find(1);
        $payment_methods    =   PaymentMethod::where('estado', 'ACTIVO')->get();
        $invoice_types      =   UtilController::getInvoiceTypes();

        return view(
            'sales.sale_document.create',
            compact(
                'customers',
                'categories',
                'brands',
                'urlImagen',
                'company',
                'types_identity_documents',
                'departments',
                'districts',
                'provinces',
                'payment_methods',
                'company_invoice'
            )
        );
    }

    public function getProductos(Request $request)
    {

        $category_id   =   $request->get('category_id');
        $brand_id      =   $request->get('brand_id');

        $products = DB::table('products as p')
            ->leftJoin('warehouse_products as wp', function ($join) {
                $join->on('wp.product_id', '=', 'p.id')
                    ->where('wp.warehouse_id', '=', 1); // Filtrar por almacen_id = 1
            })
            ->join('brands as b', 'b.id', '=', 'p.brand_id')
            ->join('categories as c', 'c.id', '=', 'p.category_id')
            ->select(
                'p.id',
                'p.brand_id',
                'p.category_id',
                'p.name',
                'p.stock_min',
                'p.code_factory',
                'p.code_bar',
                'c.name as category_name',
                'b.name as brand_name',
                'wp.stock',
                'p.sale_price',
                'p.purchase_price'
            )->where('wp.stock', '>', '0');


        if ($category_id) {
            $products  =   $products->where('p.category_id', $category_id);
        }

        if ($brand_id) {
            $products  =   $products->where('p.brand_id', $brand_id);
        }

        $products  =   $products->get();


        return DataTables::of($products)
            ->make(true);
    }

    public function validateStock(Request $request)
    {
        try {

            $product    =   DB::select(
                'select
                            wp.*
                            from warehouse_products as wp
                            where
                            wp.product_id = ?
                            and wp.warehouse_id = "1"',
                [$request->get('product_id')]
            );

            if (count($product) === 0) {
                throw new Exception("EL PRODUCTO NO EXISTE EN LA BD!!");
            }

            if ($product[0]->stock < $request->get('cant')) {

                $message    =   "EL STOCK (" . $product[0]->stock . "), ES MENOR A LA CANTIDAD (" . $request->get('cant') . ")";

                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'stock' => $product[0]->stock
                ]);
            }

            return response()->json(['success' => true, 'message' => "CANTIDAD VÁLIDA"]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage(), 'stock' => 0]);
        }
    }


    /*
array:3 [ // app\Http\Controllers\Tenant\SaleController.php:119
    "lstSale"           =>  "[{"id":1,"brand_id":3,"category_id":3,"name":"PAPA LAYS","stock_min":1,"code_factory":"","code_bar":"","category_name":"SNACKS","brand_name":"LAYS","stock":"100.00","sale_price":"12.00","purchase_price":"11.00","cant":1}]"
    "type_sale"         =>  "127"    --REQUEST AND COMPLEJA
    "customer_id"       =>  "1"      --REQUEST AND COMPLEJA
    "user_recorder_id"  =>  "1"   --VALIDACIÓN COMPLEJA
    "igv_percentage"    =>  "18.0000"
    "lstPays"           => "[{"method_pay":1,"amount":"14"},{"method_pay":"3","amount":"20"}]"
]
*/
    public function store(SaleStoreRequest $request)
    {

        DB::beginTransaction();
        try {

            $data   =   $request->toArray();

            $sale   =   $this->s_sale->store($data);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "VENTA REGISTRADA",
                'data' => (object)['sale_id' => $sale->id]
            ]);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage(), 'line' => $th->getLine(), 'file' => $th->getFile()]);
        }
    }

    public function pdf_voucher($sale_id, $size = 0)
    {
        try {

            $company                =   Company::find(1);
            $sale                   =   Sale::findOrFail($sale_id);
            $sale_products          =   SaleProduct::where('sale_id', $sale_id)->get();
            $sale_dishes            =   SaleDish::where('sale_id', $sale_id)->get();

            $route_view             =   'sales.sale_document.pdf.pdf';
            $pdf_size               =   null;

            $data_qr                =   (object)[
                'ruc_emisor'        =>  $company->ruc,
                'tipo_comprobante'  =>  $sale->type_sale_code,
                'serie'             =>  $sale->serie,
                'correlativo'       =>  $sale->correlative,
                'mto_total_igv'     =>  number_format($sale->igv_amount, 2, '.', ''),
                'total'             =>  number_format($sale->total, 2, '.', ''),
                'fecha_emision'     =>  \Carbon\Carbon::parse($sale->created_at)->format('Y-m-d'),
                'tipo_documento_adquiriente'    =>  $sale->customer_document_code,
                'nro_documento_adquieriente'    =>  $sale->customer_document_number
            ];

            $res_qr         =   QRController::generateQr(json_encode($data_qr));
            $res_qr         =   $res_qr->getData();

            if ($res_qr->success) {
                $sale->ruta_qr =   $res_qr->data->ruta_qr;
                $sale->update();
            }

            $customer       =   Customer::find($sale->customer_id);

            if ((int)$size === 0) {
                $pdf_size   =   [0, 0, 226.772, 651.95];
            } else {
                $pdf_size   =   'A4';
                $route_view =   'sales.sale_document.pdf.pdf-a4';
            }

            $pdf = PDF::loadview($route_view, [
                'company'               =>  $company,
                'sale'                  =>  $sale,
                'customer'              =>  $customer,
                'sale_products'         =>  $sale_products,
                'sale_dishes'           =>  $sale_dishes
            ]);

            $pdf->setPaper($pdf_size);

            return $pdf->stream($sale->serie . '-' . $sale->correlative . '.pdf');
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage(), 'line' => $th->getLine()]);
        }
    }

    public function downloadXml($sale_document_id)
    {

        $sale_document  =   Sale::find($sale_document_id);

        $ruta_xml       =   $sale_document->ruta_xml;

        $filePath       = public_path("{$ruta_xml}");

        if (File::exists($filePath)) {
            return response()->download($filePath);
        } else {
            abort(404, 'Archivo no encontrado');
        }
    }

    public function downloadCdr($sale_document_id)
    {

        $sale_document  =   Sale::find($sale_document_id);

        $ruta_cdr       =   $sale_document->ruta_cdr;

        $filePath       = public_path("{$ruta_cdr}");


        if (File::exists($filePath)) {
            return response()->download($filePath);
        } else {
            abort(404, 'Archivo no encontrado');
        }
    }

    /*
sale_id:1
*/
    public function send_sunat(Request $request)
    {

        try {
            $sale_id   =   $request->get('sale_id');

            if (!$sale_id) {
                throw new Exception("NO SE ENCONTRÓ EL ID DEL COMPROBANTE DE PAGO");
            }

            $res            =   $this->s_sale->sendSunat($sale_id);

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

    public function convert(SaleConvertRequest $request)
    {
        DB::beginTransaction();
        try {

            $invoice    =   $this->s_sale->convert($request->toArray());

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'VENTA CONVERTIDA CON ÉXITO EN: ' . $invoice->sale . '-' . $invoice->correlative
            ]);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    /*
array:3 [ // app\Http\Controllers\Tenant\SaleController.php:408
  "motive" => "error plato"
  "sale_id" => "12"
]
*/
    public function annular(SaleCreditNoteRequest $request)
    {
        DB::beginTransaction();
        try {
            $credit_note    =   $this->s_sale->annular($request->toArray());

            $message        =   'NOTA CRÉDITO GENERADA: ' . $credit_note->serie . '-' . $credit_note->correlative;
            DB::commit();
            return response()->json(['success' => true, 'message' => $message]);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }
}
