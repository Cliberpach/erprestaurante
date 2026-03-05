<?php

namespace App\Http\Controllers\Tenant\Queries;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Api\AlertApp;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class QNotificationController extends Controller
{
    public function index()
    {
        return view('consultas.alerts.index');
    }

    public function getAll(Request $request)
    {
        $items  =   $this->queryNotifications($request);
        return DataTables::of(
            $items
        )
            ->toJson();
    }

    public function queryNotifications(Request $request)
    {
        $customer_id    =   $request->get('customer_id');
        $start_date     =   $request->get('start_date');
        $end_date       =   $request->get('end_date');

        $items =    AlertApp::from('alerts_app as a')
            ->leftJoin('alerts_sales as as','as.alert_id','a.id')
            ->select(
                'a.id',
                'a.tenant_domain',
                'a.content',
                'a.sent_at',
                'a.status',
                'a.type',
                'a.created_at',
                'a.consumer_user_name',
                'a.consumer_date',
                'as.sale_id',
                'as.sale_serie'
            )
            ->orderByDesc('a.id');

        if ($start_date) {
            $items->whereDate('a.created_at', '>=', $start_date);
        }
        if ($end_date) {
            $items->whereDate('a.created_at', '<=', $end_date);
        }

        return $items;
    }
}
