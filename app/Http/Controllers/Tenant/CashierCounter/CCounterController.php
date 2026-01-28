<?php

namespace App\Http\Controllers\Tenant\CashierCounter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\CashierCounter\InvoiceStoreRequest;
use App\Http\Requests\Tenant\WaiterCounter\WaiterCounterStoreRequest;
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
        $view   =   $this->s_manager->chargeCreate($order);

        return $view;
    }

    public function getAll(Request $request)
    {
        $items =    Reservation::from('reservations as r')
            ->join('orders as o', 'o.id', 'r.order_id')
            ->join('tables as t', 't.id', 'o.table_id')
            ->where('r.status', 'OCUPADO')
            ->select(
                'o.id as order_id',
                't.name as table_name',
                'o.code as order_code',
                'o.created_at',
                'o.creator_user_name',
                'o.customer_name',
                'r.status',
                'o.total'
            );

        return DataTables::of($items)->make(true);
    }

    /*
array:4 [ // app\Http\Services\Tenant\CCounter\Counter\CounterService.php:29
  "customer_id" => "1"
  "lst_pays" => "[{"paymentId":"2","amount":0},{"paymentId":"1","amount":8},{"paymentId":"3","amount":30},{"paymentId":"4","amount":10}]"
  "order_id" => "1"
  "invoice_id" => "65"
]
*/
    public function storeInvoice(InvoiceStoreRequest $request)
    {
        DB::beginTransaction();
        try {

            $invoice    =   $this->s_manager->storeInvoice($request->toArray());
            $pdf_url    =   route('tenant.ventas.comprobante_venta.pdf_voucher', ['id' => $invoice->id]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'COMPROBANTE GENERADO CON ÉXITO',
                'pdf_url' => $pdf_url
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
