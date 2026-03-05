<?php

namespace App\Http\Controllers\Tenant\CashierCounter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\CashierCounter\InvoiceStoreRequest;
use App\Http\Services\Tenant\CCounter\Counter\CounterManager;
use App\Models\Tenant\Reservation\Reservation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class CCounterController extends Controller
{
    private CounterManager $s_manager;

    public function __construct()
    {
        $this->s_manager    =   new CounterManager();
    }

    public function index(): View
    {
        return view('cashier_counter.counter.index');
    }

    public function chargeCreate(int $order)
    {
        try {
            $view   =   $this->s_manager->chargeCreate($order);
            return $view;
        } catch (Throwable $th) {
            Session::flash('message_error', $th->getMessage());
            return back();
        }
    }

    public function getAll(Request $request)
    {
        $filter_status      =   $request->get('status');
        $filter_customer    =   $request->get('customer_id');
        $filter_start_date  =   $request->get('start_date');
        $filter_end_date    =   $request->get('end_date');

        $items =    Reservation::from('reservations as r')
            ->join('orders as o', 'o.id', 'r.order_id')
            ->join('tables as t', 't.id', 'o.table_id')
            ->select(
                'o.id as order_id',
                't.name as table_name',
                'o.code as order_code',
                'o.created_at',
                'o.creator_user_name',
                'o.customer_name',
                'r.status',
                'o.total',
                'o.customer_id',
                'o.code',
                'o.cashier_name',
                'o.payref_img_url'
            );

        if ($filter_status) {
            $items->where('r.status', $filter_status);
        }
        if ($filter_customer) {
            $items->where('o.customer_id', $filter_customer);
        }
        if ($filter_start_date) {
            $items->whereDate('o.created_at', '>=', $filter_start_date);
        }
        if ($filter_end_date) {
            $items->whereDate('o.created_at', '<=', $filter_end_date);
        }

        return DataTables::of($items)
            ->editColumn('created_at', function ($row) {
                return $row->created_at
                    ->timezone(config('app.timezone'))
                    ->format('Y-m-d H:i:s');
            })
            ->make(true);
    }

    /*
array:5 [ // app\Http\Controllers\Tenant\CashierCounter\CCounterController.php:99
  "customer_id" => "1"
  "lst_pays" => "[{"paymentId":"2","amount":24}]"
  "order_id" => "7"
  "lstAlertsSelected" => "[{"id":2,"content":"Yape! COMERCIAL ANDINA EIRL te envió un pago por S/ 35.00","created_at":"2026-03-04 15:18:36"},{"id":31,"content":"Yape! COMERCIAL AMAZONAS EIRL te envió un pago por S/ 77.00","created_at":"2026-03-04 15:18:36"}]"
  "invoice_id" => "67"
]
*/
    public function storeInvoice(InvoiceStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $invoice    =   $this->s_manager->storeInvoice($request->toArray());
            //$pdf_url    =   route('tenant.ventas.comprobante_venta.pdf_voucher', ['id' => $invoice->id]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'COMPROBANTE GENERADO CON ÉXITO',
                //'pdf_url' => $pdf_url
            ]);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine()
            ]);
        }
    }
}
