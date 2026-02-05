<?php

namespace App\Http\Controllers\Tenant\WaiterCounter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\WaiterCounter\WaiterCounterStoreRequest;
use App\Http\Services\Tenant\WCounter\Counter\CounterManager;
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
        return view('waiter_counter.counter.index');
    }

    public function getAll(Request $request)
    {
        $free_tables =   Table::from('tables as t')
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
            );

        return DataTables::of($free_tables)->make(true);
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
array:5 [ // app\Http\Controllers\Tenant\WaiterCounter\WCounterController.php:136
  "_token" => "nLC9ESLc8E5XI4HuR8lNMcpju2xHbHRCXUsXfm0q"
  "_method" => "PUT"
  "client_id" => "1"
  "lst_detail" => "[
  {"id":1,"name":"BUJIA","purchase_price":"1.000000","quantity":1,"sale_price":"1.000000","stock":null,"total":"1.000000","type_item":"PRODUCTO","type_name":"REPUESTO-NACIONAL"},
  {"id":3,"name":"CEVICHE DE CONCHAS","purchase_price":"1.000000","quantity":1,"sale_price":"20.000000","stock":null,"total":"20.000000","type_item":"PLATO","type_name":"ENTRADA"}]"
  "table_id" => "2"
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
}
