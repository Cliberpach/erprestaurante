<?php

namespace App\Http\Controllers\Tenant\WaiterCounter;

use App\Http\Controllers\Controller;
use App\Http\Services\Tenant\WCounter\Counter\CounterManager;
use App\Models\Tenant\Supply\Table\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                    ->where('r.status', '=', 'OCUPADA');
            })
            ->leftJoin('orders as o', 'o.id', 'r.order_id')
            ->select(
                't.name as table_name',
                'r.code as reservation_code',
                'r.order_id',
                'r.created_at as reservation_date',
                DB::raw("
                    CASE
                        WHEN r.status = 'OCUPADA' THEN 'OCUPADA'
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

/*
array:10 [ // app\Http\Services\Tenant\Orders\OrderValidation.php:64
  "_token" => "6pRzBlylQlHD4KyHQXpuG1Rpt5jWFH9BWIOcq3ER"
  "_method" => "POST"
  "client_id" => "2"
  "producto" => null
  "item_stock" => null
  "purchase_price" => null
  "sale_price" => null
  "cantidad" => null
  "lst_detail" => "[{"id":1,"name":"INCA COLA ZERO","type_name":"GASEOSAS-INCA COLA","purchase_price":"1.00","sale_price":"2.60","type_item":"PRODUCTO","quantity":"2","total":5.2},{"id":25,"name":"Flan casero","type_name":"POSTRES","purchase_price":"2.500000","sale_price":"5.000000","type_item":"PLATO","quantity":"4","stock":"80.00","total":20}]"
  "table_id" => "22"
]
*/
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

            $this->s_manager->store($request->toArray());

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
