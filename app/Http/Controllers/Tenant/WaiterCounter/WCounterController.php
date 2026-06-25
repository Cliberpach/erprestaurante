<?php

namespace App\Http\Controllers\Tenant\WaiterCounter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\WaiterCounter\WaiterCounterAddPayRequest;
use App\Http\Requests\Tenant\WaiterCounter\WaiterCounterStoreRequest;
use App\Http\Services\Tenant\Supply\Table\TableService;
use App\Http\Services\Tenant\WCounter\Counter\CounterManager;
use App\Models\Tenant\PaymentMethod;
use App\Models\Tenant\Supply\Table\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class WCounterController extends Controller
{
    private CounterManager $s_manager;

    public function __construct()
    {
        $this->s_manager    =   new CounterManager();
    }

    public function index(): View
    {
        $payment_methods    =   PaymentMethod::where('estado', 'ACTIVO')->get();
        return view('waiter_counter.counter.index', compact('payment_methods'));
    }

    public function getAll(Request $request)
    {
        try {
            $tables =   Table::from('tables as t')
                ->leftJoin('reservations as r', function ($join) {
                    $join->on('r.table_id', '=', 't.id')
                        ->where('r.status', '=', 'OCUPADO');
                })
                ->leftJoin('orders as o', 'o.id', 'r.order_id')
                ->select(
                    't.name as table_name',
                    'r.code as reservation_code',
                    'r.order_id',
                    'r.created_at as reservation_date',
                    DB::raw("
                        CASE
                            WHEN r.status = 'OCUPADO' THEN 'OCUPADO'
                            ELSE 'LIBRE'
                        END AS status
                    "),
                    'o.code as order_code',
                    'o.customer_name',
                    'o.customer_type_document_abbreviation',
                    'o.customer_document_number',
                    'o.total',
                    'o.creator_user_name',
                    't.id as table_id',
                )->where('t.status', '<>', 'ANULADO');

            if ($request->get('status') === 'OCUPADO') {
                $tables->where('r.status', $request->get('status'));
            }
            if ($request->get('status') === 'LIBRE') {
                $tables->whereNull('r.status');
            }

            if ($request->get('status') === 'all' || !$request->get('status')) {
                $tables->orderByRaw("
                    CASE
                        WHEN r.status = 'OCUPADO' AND o.creator_user_id = ? THEN 0
                        WHEN r.status = 'OCUPADO' THEN 1
                        ELSE 2
                    END ASC
                ", [auth()->id()]);
            }

            $counts = DB::table('tables as t')
                ->leftJoin('reservations as r', function ($join) {
                    $join->on('r.table_id', '=', 't.id')->where('r.status', '=', 'OCUPADO');
                })
                ->where('t.status', '<>', 'ANULADO')
                ->selectRaw("
                    COUNT(*) as total,
                    SUM(CASE WHEN r.status = 'OCUPADO' THEN 1 ELSE 0 END) as ocupado,
                    SUM(CASE WHEN r.status IS NULL THEN 1 ELSE 0 END) as libre
                ")
                ->first();

            return DataTables::of($tables)
                ->with([
                    'success' => true,
                    'counts'  => [
                        'all'     => (int) $counts->total,
                        'libre'   => (int) $counts->libre,
                        'ocupado' => (int) $counts->ocupado,
                    ]
                ])
                ->make(true);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function create(int $table_id)
    {
        try {
            $view   =   $this->s_manager->create($table_id);
            return $view;
        } catch (Throwable $th) {
            Session::flash('message_error', $th->getMessage());
            return back();
        }
    }

    /*
array:8 [ // app\Http\Controllers\Tenant\WaiterCounter\WCounterController.php:89
  "_token" => "W2IrNvvxkcHseBAAVATFvG7aoL5CB3146mVQWkzi"
  "_method" => "POST"
  "client_id" => "1"
  "observation" => "test"
  "payment_method" => "2"
  "lst_detail" => "[{"id":3,"programming_id":7,"name":"CEVICHE DE CONCHAS","type_name":"ENTRADA","purchase_price":"3.000000","sale_price":"20.000000","type_item":"PLATO","quantity":"2","stock":"70.000000","total":40,"observation":"con aji"}]"
  "table_id" => "1"
  "voucher" =>Illuminate\Http\UploadedFile {#2462
]
*/
    public function store(WaiterCounterStoreRequest $request)
    {
        DB::beginTransaction();
        try {

            $this->s_manager->store($request->toArray());

            Session::flash('message_success', 'PEDIDO REGISTRADO CON ÉXITO');
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'PEDIDO REGISTRADO CON ÉXITO'
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

    public function getOrderTable(int $table)
    {
        DB::beginTransaction();
        try {

            $data   =   $this->s_manager->getOrderTable($table);

            return response()->json([
                'success'   => true,
                'message'   => 'PEDIDO OBTENIDO CON ÉXITO',
                'data'      =>  $data
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

    public function edit(int $id)
    {
        try {
            $view   =   $this->s_manager->edit($id);
            return $view;
        } catch (Throwable $th) {
            Session::flash('message_error', $th->getMessage());
            return back();
        }
    }

    /*
array:10 [ // app/Http/Services/Tenant/Orders/OrderService.php:88
  "_token" => "McZEPesV81wwiy2Bji9a5FWDtNdG1PTjxmLBvRWh"
  "_method" => "PUT"
  "client_id" => "1"
  "observation" => null
  "producto" => null
  "item_stock" => null
  "sale_price" => null
  "cantidad" => null
  "lst_detail" => "[
  {"order_detail_id":2,"warehouse_id":1,"id":8,"name":"GALLETAS OREO","purchase_price":"1.000000","quantity":1,"sale_price":"4.000000","stock":null,"total":"4.000000","type_item":"PRODUCTO","type_name":"SNACKS-SAN JORGE","is_new":false},
  {"order_detail_id":2,"programming_id":2,"id":35,"name":"Turrón de Doña Pepa","purchase_price":"14.000000","quantity":1,"sale_price":"38.000000","stock":null,"total":"38.000000","type_item":"PLATO","type_name":"POSTRES","is_new":false},
  {"id":33,"programming_id":2,"name":"Mazamorra Morada","type_name":"POSTRES","purchase_price":"14.000000","sale_price":"21.000000","type_item":"PLATO","quantity":"12","stock":"199.000000","total":252,"observation":""}]"
  "table_id" => "4"
]
*/
    public function update(int $id, Request $request)
    {
        DB::beginTransaction();
        try {

            $this->s_manager->update($id, $request->toArray());

            Session::flash('message_success', 'PEDIDO ACTUALIZADO CON ÉXITO');
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'PEDIDO ACTUALIZADO CON ÉXITO'
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

    public function preAccount(Request $request)
    {
        DB::beginTransaction();
        try {
            $this->s_manager->preAccount($request->get('order_id'));

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Precuenta impresa con éxito']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function getTablesFree()
    {
        try {
            $s_table        =   new TableService();
            $tables_free    =   $s_table->getTablesFree();

            return response()->json([
                'success' => true,
                'message' => 'MESAS LIBRES OBTENIDAS CON ÉXITO',
                'data' => $tables_free
            ]);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    /*
  array:2 [ // app\Http\Controllers\Tenant\WaiterCounter\WCounterController.php:222
  "order_id" => "9"
  "table_selected" => "7"
]
*/
    public function changeTable(Request $request)
    {
        DB::beginTransaction();
        try {
            $order  =   $this->s_manager->changeTable($request->toArray());
            DB::commit();
            return response()->json(['success' => true, 'message' => 'MESA CAMBIADA CON ÉXITO']);
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

    /*
array:2 [ // app\Http\Controllers\Tenant\WaiterCounter\WCounterController.php:239
  "password_delete_order" => "ASDASDAS"
  "_method" => "PUT"
]
*/
    public function destroy(Request $request, int $id)
    {
        DB::beginTransaction();
        try {
            $order  =   $this->s_manager->destroy($request->toArray(), $id);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'PEDIDO ELIMINADO CON ÉXITO']);
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

    /*
array:3 [ // app\Http\Controllers\Tenant\WaiterCounter\WCounterController.php:281
  "payment_method" => "2"
  "_method" => "PUT"
  "voucher" =Illuminate\Http\UploadedFile {#2363}
]
*/
    public function addPay(WaiterCounterAddPayRequest $request, int $id)
    {
        DB::beginTransaction();
        try {
            $order  =   $this->s_manager->addPay($request->toArray(), $id);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Pago guardado en pedido con éxito']);
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
