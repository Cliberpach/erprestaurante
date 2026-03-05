<?php

namespace App\Http\Services\Tenant\Dashboard\Dashboard;

use Illuminate\Http\Request;

class DashboardManager
{
    public DashboardMarketService $s_dashboard_market;
    public DashboardGrifoService  $s_dashboard_grifo;

    public object $data;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->s_dashboard_market   =   new DashboardMarketService();
        $this->s_dashboard_grifo    =   new DashboardGrifoService();
    }

    public function getData(string $establecimiento, string $anio, string $mes): object
    {

        if ($establecimiento === 'MARKET') {
            $this->data     =   $this->s_dashboard_market->getData($anio, $mes);
        }

        if ($establecimiento === 'GRIFO') {
            $this->data     =   $this->s_dashboard_grifo->getData($anio, $mes);
        }

        return $this->data;
    }

    public function getStockMin(array $filters)
    {

        $establecimiento    =   $filters['establecimiento'] ?? null;

        if ($establecimiento === 'MARKET') {
            $this->data     =   $this->s_dashboard_market->getStockMin($filters);
        }

        return $this->data;
    }

    public function excelDishMonth(array $data)
    {
        return $this->s_dashboard_market->excelDishMonth($data);
    }

    public function excelProductsMonth(array $data)
    {
        return $this->s_dashboard_market->excelProductsMonth($data);
    }

    public function excelPaymentsMonth(array $data)
    {
        return $this->s_dashboard_market->excelPaymentsMonth($data);
    }

    public function excelCostCenterMonth(array $data)
    {
        return $this->s_dashboard_market->excelCostCenterMonth($data);
    }

    public function excelRankingWaiterMonth(array $data)
    {
        return $this->s_dashboard_market->excelRankingWaiterMonth($data);
    }

    public function excelProductsStockMin(array $data)
    {
        return $this->s_dashboard_market->excelProductsStockMin($data);
    }

    public function peakHourAnalysis()
    {
        return $this->s_dashboard_market->peakHourAnalysis();
    }
}
