<?php

namespace App\Http\Controllers\Tenant\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Services\Tenant\Dashboard\Dashboard\DashboardManager;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\View\View as View;
use Throwable;

class DashboardController extends Controller
{
    private   DashboardManager $s_dashboard;

    public function __construct()
    {
        $this->s_dashboard       =   new DashboardManager();
    }

    public function index(): View
    {
        return view('dashboard.dashboard.index');
    }

    /*
array:3 [ // app\Http\Controllers\General\PanelControl\Dashboard\DashboardController.php:16
  "establecimiento" => "MARKET"
  "anio" => "2025"
  "mes" => "4"
]
*/
    public function getData(Request $request)
    {
        try {
            $establecimiento    =   $request->get('establecimiento');
            $anio               =   $request->get('anio');
            $mes                =   $request->get('mes');

            $res                =   $this->s_dashboard->getData($establecimiento, $anio, $mes);

            return response()->json(['success' => true, 'message' => 'DATOS OBTENIDOS', 'data' => $res]);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage(), 'line' => $th->getLine(), 'file' => $th->getFile()]);
        }
    }


    public function getStockMin(Request $request)
    {
        $productos  =   $this->s_dashboard->getStockMin($request->toArray());
        return DataTables::of($productos)->make(true);
    }

    public function excelPlatosMes(Request $request)
    {
        return $this->s_dashboard->excelDishMonth($request->toArray());
    }

    public function excelProductsMonth(Request $request)
    {
        return $this->s_dashboard->excelProductsMonth($request->toArray());
    }

    public function excelPaymentsMonth(Request $request)
    {
        return $this->s_dashboard->excelPaymentsMonth($request->toArray());
    }

    public function excelCostCenterMonth(Request $request)
    {
        return $this->s_dashboard->excelCostCenterMonth($request->toArray());
    }

    public function excelRankingWaiterMonth(Request $request)
    {
        return $this->s_dashboard->excelRankingWaiterMonth($request->toArray());
    }

    public function excelProductosStockMin(Request $request)
    {
        return $this->s_dashboard->excelProductsStockMin($request->toArray());
    }

    public function peakHourAnalysis(Request $request)
    {
        try {
            $info   = $this->s_dashboard->peakHourAnalysis($request->toArray());
            return response()->json(['success' => true, 'message' => 'Horas pico obtenidas', 'data' => $info]);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }
}
