<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FormatController;
use App\Http\Controllers\UtilController;
use App\Http\Requests\Sale\SaleStoreRequest;
use App\Http\Requests\Tenant\Sale\SaleConvertRequest;
use App\Http\Requests\Tenant\Sale\SaleCreditNoteRequest;
use App\Http\Services\Tenant\Sale\Sale\SaleManager;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Company;
use App\Models\Landlord\Customer;
use App\Models\Tenant\Maintenance\Company\CompanyInvoice;
use App\Models\Tenant\PaymentMethod;
use App\Models\Tenant\Sales\Sale\Sale;
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
        $invoice_types              =   UtilController::getInvoiceTypes()->whereIn('id', ['65', '66']);
        $customer_formatted         =   FormatController::getFormatInitialCustomer(1);
        $vars_mdl_customer          =   UtilController::getVarsMdlCustomer();
        $filter_invoices            =   UtilController::getInvoiceTypes()->whereIn('id', ['65', '66', '67']);

        $vars                       =   array_merge(
            compact(
                'invoice_types',
                'customer_formatted',
                'filter_invoices'
            ),
            $vars_mdl_customer
        );
        return view('sales.sale_document.index', $vars);
    }

    public function getSales(Request $request)
    {
        $filter_customer    =   $request->get('customer_id');
        $filter_start_date  =   $request->get('start_date');
        $filter_end_date    =   $request->get('end_date');
        $filter_sunat       =   $request->get('sunat_status');
        $filter_type_sale   =   $request->get('type_sale');

        $sales    =   DB::table('sales as s')
            ->select(
                's.id',
                's.created_at as fecha_registro',
                's.customer_id',
                's.customer_name',
                DB::raw("CONCAT('CM-', LPAD(s.petty_cash_book_id, 8, '0')) as cash_book_code"),
                's.creator_user_name',
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
                's.petty_cash_name',
                's.converted_to_serie',
                's.converted_from_serie',
                's.summary_id',
                's.summary_serie',
                DB::raw("(
                    SELECT CONCAT(
                        '[',
                        GROUP_CONCAT(
                            CONCAT(
                                '{\"name\":\"', sp.payment_method_name, '\",',
                                '\"amount\":', sp.amount,
                                '}'
                            )
                        ),
                        ']'
                    )
                    FROM sales_pays sp
                    WHERE sp.sale_id = s.id
                    AND sp.status = 'ACTIVO'
                ) as payments")
            );

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
        if (in_array($filter_type_sale, ['65', '66', '67'])) {
            $sales->where('s.type_sale_id', $filter_type_sale);
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
            ->filterColumn('cash_book_code', function ($query, $keyword) {
                $query->whereRaw("CONCAT('CM-', LPAD(s.petty_cash_book_id, 8, '0')) LIKE ?", ["%{$keyword}%"]);
            })
            ->make(true);
    }

    public function create()
    {
        $have_module_customer_accounts  =   UtilController::haveModuleCustomerAccounts();
        $categories                     =   Category::where('status', 'ACTIVE');
        $brands                         =   Brand::where('status', 'ACTIVE');
        $customers                      =   Customer::where('status', 'ACTIVO')->get();
        $company                        =   Company::findOrFail(1);
        $payment_conditions             =   UtilController::getPaymentConditions();

        if (!$have_module_customer_accounts) {
            $payment_conditions         =   $payment_conditions->where('id', 1);
        }

        $company_invoice    =   CompanyInvoice::find(1);
        $invoice_types      =   UtilController::getInvoiceTypes()->whereIn('id', ['65', '66', '67']);
        $payment_methods    =   PaymentMethod::where('estado', 'ACTIVO')->get();
        $customer_formatted =   FormatController::getFormatInitialCustomer(1);

        $vars_mdl_customer          =   UtilController::getVarsMdlCustomer();

        $vars   =   array_merge(
            compact(
                'customers',
                'categories',
                'brands',
                'company',
                'payment_methods',
                'company_invoice',
                'invoice_types',
                'customer_formatted',
                'payment_conditions',
            ),
            $vars_mdl_customer
        );

        return view(
            'sales.sale_document.create',
            $vars
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

    /*
array:9 [ // app\Http\Services\Tenant\Sale\Sale\SaleService.php:38
  "_token" => "Ucg9W52nqXOk9r5X7fseL0IV70KvfemnNoJr9aoE"
  "type_sale" => "65"
  "payment_condition_id" => "1"
  "registration_date" => "2026-02-10"
  "expiration_date" => "2026-02-10"
  "customer_id" => "1"
  "lstSale" => "[{"id":20,"brand_id":1,"category_id":4,"name":"YOGURT FRESA","stock_min":1,"code_factory":null,"code_bar":null,"category_name":"GASEOSAS","brand_name":"GLORIA","stock":"200.00","sale_price":"5.00","purchase_price":"7.00","cant":1},{"id":29,"brand_id":1,"category_id":5,"name":"GOMITAS","stock_min":1,"code_factory":null,"code_bar":null,"category_name":"AGUAS","brand_name":"GLORIA","stock":"200.00","sale_price":"4.00","purchase_price":"1.00","cant":1},{"id":11,"brand_id":2,"category_id":4,"name":"GASEOSA COLA 500ML","stock_min":1,"code_factory":null,"code_bar":null,"category_name":"GASEOSAS","brand_name":"COCA COLA","stock":"200.00","sale_price":"5.00","purchase_price":"5.00","cant":1}]"
  "lstPays" => "[{"method_pay":1,"amount":14}]"
  "igv_percentage" => "18.0000"
]
*/
    public function store(SaleStoreRequest $request)
    {

        DB::beginTransaction();
        try {

            $sale   =   $this->s_sale->store($request->toArray());

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

            $res    =   $this->s_sale->pdf_voucher($sale_id, $size);
            $pdf    =   $res['pdf'];
            $name   =   $res['pdf_name'];
            return $pdf->stream($name . '.pdf');
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
    {dd('test');
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
