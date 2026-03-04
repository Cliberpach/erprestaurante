<?php

namespace App\Http\Controllers\Tenant\Queries;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Alerts\Alert;
use App\Models\Tenant\Api\AlertApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            ->select([
                'a.id',
                'a.tenant_domain',
                'a.content',
                'a.sent_at',
            ])
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
