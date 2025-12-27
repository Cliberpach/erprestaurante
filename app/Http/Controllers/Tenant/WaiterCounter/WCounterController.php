<?php

namespace App\Http\Controllers\Tenant\WaiterCounter;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Supply\Table\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class WCounterController extends Controller
{
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
            ->leftJoin('orders as o', 'o.id','r.order_id')
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
}
