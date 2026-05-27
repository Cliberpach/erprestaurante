<?php

namespace App\Http\Controllers\Tenant\Accounts;

use App\Exports\Tenant\Accounts\CustomerAccount\CustomerAccountExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Accounts\CustomerAccount\PayStoreRequest;
use App\Http\Services\Tenant\Accounts\CustomerAccount\CustomerAccountManager;
use App\Models\Company;
use App\Models\Landlord\Customer;
use App\Models\Tenant\Accounts\CustomerAccountDetail;
use App\Models\Tenant\PaymentMethod;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;
use Yajra\DataTables\DataTables;

class CustomerAccountController extends Controller
{
    private CustomerAccountManager $s_account;

    public function __construct()
    {
        $this->s_account    =   new CustomerAccountManager();
    }

    public function index()
    {
        $payment_methods    =   PaymentMethod::where('estado', 'ACTIVO')->get();

        return view('accounts.customer_accounts.index', compact('payment_methods'));
    }

    private function queryAll(Request $request)
    {
        $query = DB::table('customer_accounts as ca')
            ->leftJoin('sales as s', 's.id', 'ca.sale_id')
            ->leftJoin('erprestaurante.customers as c', 'c.id', 's.customer_id')
            ->select(
                'ca.id',
                'ca.document_serie',
                DB::raw("CONCAT(c.type_document_abbreviation, ':', c.document_number, '-', c.name) as customer_info"),
                'ca.document_date',
                'ca.amount',
                'ca.paid',
                'ca.balance',
                'ca.status'
            )
        ;

        if ($request->get('customer')) {
            $query->where('s.customer_id', $request->get('customer'));
        }
        if ($request->get('status')) {
            $query->where('ca.status', $request->get('status'));
        } else {
            $query->where('ca.status', '<>', 'ANULADO');
        }
        if ($request->get('start_date')) {
            $query->where('ca.document_date', '>=', $request->get('start_date'));
        }
        if ($request->get('end_date')) {
            $query->where('ca.document_date', '<=', $request->get('end_date'));
        }

        return $query;
    }

    public function getCustomerAccounts(Request $request)
    {
        $query = $this->queryAll($request);

        return DataTables::of($query)
            ->filterColumn('customer_info', function ($q, $keyword) {
                $q->whereRaw(
                    "CONCAT(c.type_document_abbreviation, ':', c.document_number, '-', c.name) LIKE ?",
                    ["%{$keyword}%"]
                );
            })
            ->make(true);
    }

    public function pdfAll(Request $request)
    {
        $company  = Company::findOrFail(1);
        $data     = $this->queryAll($request)->get();

        $customer = null;
        if ($request->get('customer')) {
            $customer = Customer::find($request->get('customer'));
        }
        $request->merge(['customer' => $customer]);

        $pdf = Pdf::loadView('accounts.customer_accounts.reports.pdf-all', [
            'company' => $company,
            'data'    => $data,
            'filters' => $request,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('cuentas_cliente_' . Carbon::now()->format('Y_m_d_H_i_s') . '.pdf');
    }

    public function excelAll(Request $request)
    {
        $company  = Company::findOrFail(1);
        $data     = $this->queryAll($request)->get();

        $customer = null;
        if ($request->get('customer')) {
            $customer = Customer::find($request->get('customer'));
        }
        $request->merge(['customer' => $customer]);

        return Excel::download(
            new CustomerAccountExport($data, $request, $company),
            'cuentas_cliente_' . Carbon::now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $this->s_account->store($request->toArray());
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function getCustomerAccount(int $id)
    {
        try {

            $cuenta = DB::table('customer_accounts as ca')
                ->leftJoin('sales as s', 's.id', 'ca.sale_id')
                ->leftJoin('erprestaurante.customers as c', 'c.id', 's.customer_id')
                ->select(
                    'ca.id',
                    'ca.document_serie',
                    'ca.document_date',
                    'ca.paid',
                    DB::raw("CONCAT(c.type_document_abbreviation, ':', c.document_number, '-', c.name) as customer_info"),
                    'ca.amount',
                    'ca.paid',
                    'ca.balance',
                    'ca.status',
                    'ca.work_order_id',
                    'ca.instance_serie',
                    'ca.type_instance',
                    's.id as sale_id'
                )
                ->where('ca.id', $id)
                ->first();

            if (!$cuenta) {
                throw new Exception("CUENTA CLIENTE NO EXISTE EN LA BD");
            }

            $detalle    =   CustomerAccountDetail::where('customer_account_id', $id)
                ->orderByDesc('id')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'CUENTA CLIENTE OBTENIDA',
                'data' => [
                    'cuenta' => $cuenta,
                    'detalle' => $detalle
                ]
            ]);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    /*
array:11 [ // app\Http\Controllers\Tenant\Accounts\CustomerAccountController.php:115
  "_token" => "OUiVLJK4B1xxUcncLt4KMQWShWUygPIGMkm5ZTu4"
  "pago" => "A CUENTA"
  "fecha" => "2025-12-05"
  "cantidad" => "10.00"
  "observacion" => "test"
  "efectivo_venta" => "0"
  "modo_pago" => "3"
  "cuenta" => "2"
  "nro_operacion" => "asd123"
  "importe_venta" => "10"
  "url_imagen" => null
]
*/
    public function storePago(PayStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->s_account->storePago($request->toArray());
            DB::commit();
            return response()->json(['success' => true, 'message' => 'PAGO REGISTRADO CON ÉXITO']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage(), 'line' => $th->getLine(), 'file' => $th->getFile()]);
        }
    }

    public function pdfOne(int $id)
    {
        try {
            $pdf    =   $this->s_account->pdfOne($id);
            return $pdf;
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }
}
