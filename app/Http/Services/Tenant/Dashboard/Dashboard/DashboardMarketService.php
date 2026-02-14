<?php

namespace App\Http\Services\Tenant\Dashboard\Dashboard;

use App\Exports\Tenant\Dashboard\CostCenterMonthExport;
use App\Exports\Tenant\Dashboard\DishesMonthExport;
use App\Exports\Tenant\Dashboard\PaymentMethodMonthExport;
use App\Exports\Tenant\Dashboard\ProductoStockMinExport;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DashboardMarketService
{
    private array $data =   [];
    private DashboardRepository $s_repository;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->s_repository    =   new DashboardRepository();
    }

    public function getData(string $year, string $month): object
    {
        $desde = Carbon::create($year, $month, 1)->startOfMonth();
        $hasta = Carbon::create($year, $month, 1)->endOfMonth();

        $this->data['data_carousel']    =   $this->getDataCarousel($desde, $hasta);
        $this->data['data_graficos']    =   $this->getDataGraficos($desde, $hasta, $year);

        //$this->data['data_analisis']    =   $this->getDataAnalisis($anio, $mes);

        return (object)$this->data;
    }

    public function getDataCarousel($desde, $hasta): object
    {
        $res_1                  =   $this->s_repository->getSalesCarousel($desde, $hasta);
        $res_1->utility_total   =   $res_1->utility_products + $res_1->utility_dishes;

        $res_2                          =   $this->s_repository->getSupplierAccountsCarousel($desde, $hasta);
        $res_1->total_supplier_accounts =   $res_2;

        $data   =   $res_1;
        return $data;
    }

    public function getDataGraficos(string $desde, string $hasta, $year)
    {
        $data_graficos  =   (object)[
            'productos'         =>  $this->s_repository->getProductosMes($desde, $hasta),
            'platos'            =>  $this->s_repository->getPlatosMes($desde, $hasta),
            'payment_methods'   =>  $this->s_repository->getPaymentMethodMonth($desde, $hasta),
            'cost_center'       =>  $this->s_repository->getCostCenterMonth($desde, $hasta),
            'waiter_ranking'    =>  $this->s_repository->getWaiterRankingMonth($desde, $hasta),
            //'cuentas_cobrar'  =>  $this->getCuentasCobrar(),
            'cuentas_pagar'     =>  $this->s_repository->getCuentasPagar(),
            'ventas_vs_compras' =>  $this->s_repository->getVentasVsComprasAnio($year),
            'kpi_ventas'        =>  $this->s_repository->kpiDayMonthYear()
        ];
        return $data_graficos;
    }

    public function getDataAnalisis(string $anio, string $mes): array
    {
        $data_analisis  =   [
            'analisis_rentabilidad' =>  $this->getAnalisisRentabilidad($anio, $mes),
            'analisis_tributario'   =>  $this->getAnalisisTributario($anio, $mes),
            'analisis_eficiencia'   =>  $this->getAnalisisEficiencia($anio, $mes),
            'analisis_existencia'   =>  $this->getAnalisisExistencia()
        ];

        return $data_analisis;
    }

    public function getVentasMes(string $anio, string $mes): float
    {
        $ventas_mes =   DB::select('SELECT
                        SUM(v.total) as total
                        FROM sales_documents as v
                        WHERE
                        v.status = "ACTIVO"
                        AND v.sunat_status NOT IN("ANULADO","BAJA","ANULADO PARCIAL")
                        AND  YEAR(v.created_at) = ?
                        AND  MONTH(v.created_at) = ?
                        ', [$anio, $mes]);

        $ventas_mes =   round($ventas_mes[0]->total, 2);

        return $ventas_mes;
    }

    public function getIgvMes(string $anio, string $mes): float
    {
        $igv_mes    =   DB::select('SELECT
                        IFNULL(ROUND(SUM(v.igv_amount),2),0) as total
                        FROM sales_documents as v
                        WHERE
                        v.status = "ACTIVO"
                        AND v.sunat_status NOT IN("ANULADO","BAJA","ANULADO PARCIAL")
                        AND  YEAR(v.created_at) = ?
                        AND  MONTH(v.created_at) = ?
                        ', [$anio, $mes]);

        $igv_mes =   round($igv_mes[0]->total, 2);

        return $igv_mes;
    }

    public function getComprasMes(string $anio, string $mes): float
    {
        $compras_mes    =   DB::select('
                            SELECT
                            SUM(c.total) as total
                            FROM purchase_documents as c
                            WHERE
                            c.estado = "ACTIVO"
                            AND  YEAR(c.created_at) = ?
                            AND  MONTH(c.created_at) = ?
                            ', [$anio, $mes]);

        $compras_mes =   round($compras_mes[0]->total, 2);

        return $compras_mes;
    }

    /*
    public function getUtilidadBrutaMes(float $ventas_mes,float $compras_mes):float{
        return $ventas_mes - $compras_mes;
    }
    */

    public function getUtilidadBrutaMes(string $anio, string $mes): float
    {

        $consulta   =   DB::select(
            'SELECT
                        IFNULL(round(
                            SUM(
                                vd.net_quantity * ( round(vd.price_sale,2) - round(p.purchase_price,2) )
                            )
                        ,2),0) as utilidad_bruta_mes
                        FROM sales_documents_details AS vd
                        JOIN sales_documents AS v ON v.id = vd.sale_document_id
                        JOIN products AS p on p.id = vd.product_id
                        WHERE
                        v.status = "ACTIVO"
                        AND v.sunat_status NOT IN("ANULADO","ANULADO PARCIAL")
                        AND YEAR(v.created_at) = ?
                        AND MONTH(v.created_at) = ?',
            [$anio, $mes]
        );

        return $consulta[0]->utilidad_bruta_mes;
    }

    public function getCantComprobantesMes(string $anio, string $mes): int
    {
        $comprobantes_mes =   DB::select('SELECT
                        COUNT(v.id) as cant
                        FROM sales_documents as v
                        WHERE
                        v.status = "ACTIVO"
                        AND v.sunat_status NOT IN("ANULADO","BAJA","ANULADO PARCIAL")
                        AND  YEAR(v.created_at) = ?
                        AND  MONTH(v.created_at) = ?
                        ', [$anio, $mes]);

        $comprobantes_mes =   $comprobantes_mes[0]->cant;

        return $comprobantes_mes;
    }

    public function getTotalBoletasMes(string $anio, string $mes): float
    {
        $boletas_mes    =   DB::select('SELECT
                            SUM(v.total) as total
                            FROM sales_documents as v
                            WHERE
                            v.status = "ACTIVO"
                            AND v.type_sale_code = "03"
                            AND v.sunat_status NOT IN("ANULADO","BAJA","ANULADO PARCIAL")
                            AND  YEAR(v.created_at) = ?
                            AND  MONTH(v.created_at) = ?
                            ', [$anio, $mes]);

        $boletas_mes =   round($boletas_mes[0]->total, 2);

        return $boletas_mes;
    }

    public function getTotalFacturasMes(string $anio, string $mes): float
    {
        $facturas_mes    =   DB::select('
                            SELECT
                            SUM(v.total) as total
                            FROM sales_documents as v
                            WHERE
                            v.status = "ACTIVO"
                            AND v.type_sale_code = "01"
                            AND v.sunat_status NOT IN("ANULADO","BAJA","ANULADO PARCIAL")
                            AND  YEAR(v.created_at) = ?
                            AND  MONTH(v.created_at) = ?
                            ', [$anio, $mes]);

        $facturas_mes =   round($facturas_mes[0]->total, 2);

        return $facturas_mes;
    }

    public function getTotalNotaCreditoMes(string $anio, string $mes): float
    {
        $notas_mes    =   DB::select('SELECT
                            COALESCE(SUM(nc.total), 0)  as total
                            FROM credit_notes as nc
                            WHERE
                            nc.sunat_status != "RECHAZADO"
                            AND  YEAR(nc.created_at) = ?
                            AND  MONTH(nc.created_at) = ?
                            ', [$anio, $mes]);

        $notas_mes =   round($notas_mes[0]->total, 2);

        return $notas_mes;
    }

    public function getCuentasCobrar(): object
    {

        $cuentas_clientes   =   DB::select('SELECT
                                CONCAT(c.type_document_abbreviation,":",c.document_number,"-",c.name) as cliente_nombre,
                                round(SUM(cc.balance),2) as cliente_saldo
                                FROM customer_accounts as cc
                                INNER JOIN erptaller.customers as c on c.id = cc.customer_id
                                WHERE
                                cc.status <> "ANULADO"
                                GROUP BY c.type_document_abbreviation,c.document_number,c.name');


        $cuentas_clientes_array =   [];
        foreach ($cuentas_clientes as $cuenta) {
            $cuentas_clientes_array[]   =   [
                $cuenta->cliente_nombre,
                round((float) $cuenta->cliente_saldo, 2)
            ];
        }

        $totales        =   DB::select('SELECT
                                ROUND(IFNULL(SUM(cc.balance), 0),2) AS pendiente,
                                ROUND(IFNULL(SUM(cc.amount - cc.balance), 0),2) AS cobrado,
                                ROUND(IFNULL(SUM(cc.amount), 0), 2) AS total
                            FROM customer_accounts as cc');

        $resultado  =   (object)['cuentas' => $cuentas_clientes_array, 'totales' => $totales[0]];

        return $resultado;
    }

    public function getStockMin(array $data)
    {
        return $this->s_repository->getProductsStockMin($data);
    }

    public function getAnalisisRentabilidad(string $anio, string $mes): object
    {
        $facturas               =   $this->getFacturasRentabilidad($anio, $mes);
        $boletas                =   $this->getBoletasRentabilidad($anio, $mes);
        $notas_venta            =   $this->getNotasVentaRentabilidad($anio, $mes);
        $notas_credito          =   $this->getNotasCreditoRentabilidad($anio, $mes);
        $totales                =   $this->getTotalesRentabilidad($facturas, $boletas, $notas_venta, $notas_credito);

        return (object)['datos' => [$facturas, $boletas, $notas_venta, $notas_credito], 'totales' => $totales];
    }

    public function getAnalisisTributario(string $anio, string $mes): object
    {

        $ventas_tributario      =   $this->getVentasTributario($anio, $mes);
        $compras_tributario     =   $this->getComprasAfectasTributario($anio, $mes);
        $renta                  =   $this->getRentaTributario($ventas_tributario->igv, $compras_tributario->igv, $ventas_tributario->total);

        $data   =   [
            (object)[
                'descripcion'       =>  'VALOR VENTA',
                'ventas'            =>  round($ventas_tributario->subtotal, 2),
                'compras_afectas'   =>  round($compras_tributario->subtotal, 2),
                'compras_inafectas' =>  0
            ],
            (object)[
                'descripcion'  => 'IGV',
                'ventas'            =>  round($ventas_tributario->igv, 2),
                'compras_afectas'   =>  round($compras_tributario->igv, 2),
                'compras_inafectas' =>  0
            ],
            (object)[
                'descripcion'       => 'TOTAL',
                'ventas'            =>  round($ventas_tributario->total, 2),
                'compras_afectas'   =>  round($compras_tributario->total, 2),
                'compras_inafectas' =>  0
            ],
        ];

        $res    =   (object)['data' => $data, 'renta' => $renta];

        return $res;
    }

    public function getRentaTributario(float $igv_ventas, float $igv_compras, float $total_ventas): object
    {
        $renta  =   Company::find(1)->renta ?? 1;
        return (object)[
            'igv_pagar' =>  $igv_ventas - $igv_compras,
            'renta'     =>  round($total_ventas * $renta, 2)
        ];
    }

    public function getVentasTributario(string $anio, string $mes): object
    {
        $ventas_tributario  =   DB::select('SELECT
                                    IFNULL(ROUND(SUM(v.subtotal),2),0) as subtotal,
                                    IFNULL(ROUND(SUM(v.igv_amount),2),0) as igv,
                                    IFNULL(ROUND(SUM(v.total),2),0) as total
                                    from sales_documents as v
                                    where
                                    v.status = "ACTIVO"
                                    AND v.sunat_status NOT IN("ANULADO","ANULADO PARCIAL","BAJA")
                                    AND YEAR(v.created_at) = ?
                                    AND MONTH(v.created_at) = ?
                                ', [$anio, $mes])[0];

        return $ventas_tributario;
    }

    public function getComprasAfectasTributario(string $anio, string $mes): object
    {
        $compras_tributario  =   DB::selectOne('SELECT
                                    IFNULL(ROUND(SUM(c.subtotal),2),0) as subtotal,
                                    IFNULL(ROUND(SUM(c.amount_igv),2),0) as igv,
                                    IFNULL(ROUND(SUM(c.total),2),0) as total
                                    from purchase_documents as c
                                    where
                                    c.estado = "ACTIVO"
                                    AND YEAR(c.created_at) = ?
                                    AND MONTH(c.created_at) = ?
                                ', [$anio, $mes]);

        return $compras_tributario;
    }

    public function getComprasInafectasTributario(string $anio, string $mes): null
    {
        return null;
    }

    public function getBoletasRentabilidad(string $anio, string $mes): object
    {

        $boletas_rentabilidad   =   $this->getComprobanteRentabilidad($anio, $mes, '03');
        return $boletas_rentabilidad;
    }

    public function getFacturasRentabilidad(string $anio, string $mes): object
    {

        $facturas_rentabiidad   =   $this->getComprobanteRentabilidad($anio, $mes, '01');

        return $facturas_rentabiidad;
    }

    public function getNotasVentaRentabilidad(string $anio, string $mes): object
    {

        $notas_rentabilidad   =   $this->getComprobanteRentabilidad($anio, $mes, 'NV');
        return $notas_rentabilidad;
    }

    public function getNotasCreditoRentabilidad(string $anio, string $mes): object
    {
        $resultado  =   DB::select(
            'SELECT
                            COUNT(DISTINCT nc.id) as operaciones,
                            SUM(nc.total) as ventas,
                            (
                                SELECT
                                    SUM(IFNULL(round(p.purchase_price,2) * round(ncd.quantity,2), 0))
                                FROM credit_notes_details ncd
                                JOIN products p ON p.id = ncd.product_id
                                JOIN credit_notes nc2 ON nc2.id = ncd.credit_note_id
                                WHERE
                                    YEAR(nc2.created_at) = ?
                                    AND MONTH(nc2.created_at) = ?
                            ) as costos
                        FROM credit_notes as nc
                        WHERE
                            YEAR(nc.created_at) = ?
                            AND MONTH(nc.created_at) = ?',
            [
                $anio,
                $mes,
                $anio,
                $mes
            ]
        );

        $data   =   $resultado[0];

        return (object)[
            'documento'         => 'NOTAS CRÉDITO',
            'operaciones'       => (int)$data->operaciones,
            'ventas'            => (float)$data->ventas,
            'costos'            => round($data->costos, 2),
            'utilidad_bruta'    => round((float)$data->ventas - (float)$data->costos, 2)
        ];
    }

    public function getComprobanteRentabilidad(string $anio, string $mes, string $codigo_doc): object
    {

        $resultado  =   DB::select(
            '
                        SELECT
                            COUNT(DISTINCT v.id) as operaciones,
                            SUM(v.total) as ventas,
                            (
                                SELECT
                                    SUM(IFNULL(round(p.purchase_price,2) * round(vd.quantity,2), 0))
                                FROM sales_documents_details vd
                                JOIN products p ON p.id = vd.product_id
                                JOIN sales_documents v2 ON v2.id = vd.sale_document_id
                                WHERE
                                    v2.status = "ACTIVO"
                                    AND v2.type_sale_code = ?
                                    AND v2.sunat_status NOT IN("ANULADO", "BAJA", "ANULADO PARCIAL")
                                    AND YEAR(v2.created_at) = ?
                                    AND MONTH(v2.created_at) = ?
                            ) as costos
                        FROM sales_documents as v
                        WHERE
                            v.status = "ACTIVO"
                            AND v.type_sale_code = ?
                            AND v.sunat_status NOT IN("ANULADO", "BAJA", "ANULADO PARCIAL")
                            AND YEAR(v.created_at) = ?
                            AND MONTH(v.created_at) = ?',
            [
                $codigo_doc,
                $anio,
                $mes,
                $codigo_doc,
                $anio,
                $mes
            ]
        );

        $data       =   $resultado[0];
        $documento  =   '';
        if ($codigo_doc === '01') {
            $documento  =   'FACTURAS';
        }
        if ($codigo_doc === '03') {
            $documento  =   'BOLETAS';
        }
        if ($codigo_doc === 'NV') {
            $documento  =   'NOTAS_DE_VENTA';
        }

        return (object)[
            'documento'         => $documento,
            'operaciones'       => (int)$data->operaciones,
            'ventas'            => (float)$data->ventas,
            'costos'            => round($data->costos, 2),
            'utilidad_bruta'    => round((float)$data->ventas - (float)$data->costos, 2)
        ];
    }

    public function getTotalesRentabilidad(object $facturas, object $boletas, object $notas_venta, object $notas_credito): object
    {
        return (object)[
            'documento'         =>  'TOTAL',
            'operaciones'       =>  $facturas->operaciones + $boletas->operaciones + $notas_venta->operaciones + $notas_credito->operaciones,
            'ventas'            =>  $facturas->ventas + $boletas->ventas + $notas_venta->ventas - $notas_credito->ventas,
            'costos'            =>  $facturas->costos + $boletas->costos + $notas_venta->costos - $notas_credito->costos,
            'utilidad_bruta'    =>  $facturas->utilidad_bruta + $boletas->utilidad_bruta + $notas_venta->utilidad_bruta - $notas_credito->utilidad_bruta
        ];
    }

    public function getAnalisisEficiencia(string $anio, string $mes): object
    {

        $res    =   (object)[
            'saldo_cobranza'        =>  $this->getSaldoCobranzaAnt($anio, $mes),
            'creditos_cobranza'     =>  $this->getCreditosCobranza($anio, $mes),
            'cobranza'              =>  $this->getCobranza($anio, $mes),
            'acumulado_cobranza'    =>  $this->getSaldoCobranza($anio, $mes),

            // 'saldo_pagar'           =>  $this->getSaldoPagarAnt($anio, $mes),
            // 'creditos_pagar'        =>  $this->getCreditosPagarMes($anio, $mes),
            // 'pagar'                 =>  $this->getPagarMes($anio, $mes),
            // 'acumulado_pagar'       =>  $this->getSaldoPagar($anio, $mes)
        ];

        return $res;
    }

    public function getCreditosCobranza(string $anio, string $mes): float
    {

        $res    =   DB::select('SELECT
                    IFNULL(SUM(v.total),0) as creditos
                    FROM sales_documents as v
                    WHERE v.status = "ACTIVO"
                    AND v.sunat_status NOT IN("ANULADO","ANULADO PARCIAL","BAJA")
                    AND v.payment_condition_id <> 1
                    AND YEAR(v.created_at) = ?
                    AND MONTH(v.created_at) = ?', [$anio, $mes]);

        return  round($res[0]->creditos, 2);
    }

    public function getCobranza(string $anio, string $mes)
    {
        //--ccd.status <> "ANULADO"
        $consulta   =   DB::select('SELECT
                        IFNULL(SUM(ccd.amount),0) as cobranza
                        FROM customer_accounts_details AS ccd
                        WHERE
                        YEAR(ccd.created_at) = ?
                        AND MONTH(ccd.created_at) = ?', [$anio, $mes]);

        return  round($consulta[0]->cobranza, 2);
    }

    public function getSaldoCobranza(string $anio, string $mes)
    {
        $saldo_ant  =   $this->getSaldoCobranzaAnt($anio, $mes);
        $credito    =   $this->getCreditosCobranza($anio, $mes);
        $cobrado    =   $this->getCobranza($anio, $mes);

        $saldo      =   $saldo_ant + $credito - $cobrado;

        return  round($saldo, 2);
    }

    public function getSaldoCobranzaAnt(string $anio, string $mes)
    {

        $credito    =   $this->getCreditosCobranzaAnt($anio, $mes);

        $cobrado    =   $this->getCobranzaAnt($anio, $mes);

        return $credito - $cobrado;
    }

    public function getAnalisisExistencia(): object
    {
        $data_existencias   =   [
            'stock_valorizado' =>  $this->getStockValorizado()
        ];

        return (object)$data_existencias;
    }

    public function getSaldoPagarAnt(string $anio, string $mes)
    {

        $credito    =   $this->getCreditosPagarAnt($anio, $mes);

        $cobrado    =   $this->getPagarAnt($anio, $mes);

        return $credito - $cobrado;
    }

    public function getCreditosPagarMes(string $anio, string $mes): float
    {

        $res    =   DB::select('SELECT
                    IFNULL(SUM(c.importe_total),0) as creditos
                    FROM compras as c
                    WHERE c.estado = "ACTIVO"
                    AND c.condicion_pago_id <> 1
                    AND YEAR(c.created_at) = ?
                    AND MONTH(c.created_at) = ?', [$anio, $mes]);

        return  round($res[0]->creditos, 2);
    }

    public function getStockValorizado(): float
    {
        $consulta   =   DB::select('SELECT
                        IFNULL(SUM(ap.stock * p.purchase_price),0) as stock_valorizado
                        FROM warehouse_products as ap
                        JOIN products as p on p.id = ap.product_id
                        WHERE ap.warehouse_id = 1
                        AND p.status = "ACTIVO"');
        return round($consulta[0]->stock_valorizado, 2);
    }

    public function getPagarMes(string $anio, string $mes)
    {

        $consulta   =   DB::select('SELECT
                        IFNULL(SUM(ccd.monto),0) as pagar
                        FROM cuentas_proveedor_detalle AS ccd
                        WHERE
                        ccd.estado <> "ANULADO"
                        AND YEAR(ccd.created_at) = ?
                        AND MONTH(ccd.created_at) = ?', [$anio, $mes]);

        return  round($consulta[0]->pagar, 2);
    }

    public function getSaldoPagar(string $anio, string $mes)
    {

        $saldo_ant  =   $this->getSaldoPagarAnt($anio, $mes);
        $credito    =   $this->getCreditosPagarMes($anio, $mes);
        $cobrado    =   $this->getPagarMes($anio, $mes);

        $saldo      =   $saldo_ant + $credito - $cobrado;

        return  round($saldo, 2);
    }

    public function getCreditosPagarAnt(string $anio, string $mes): float
    {

        $res    =   DB::select('SELECT
                    IFNULL(SUM(c.importe_total),0) as creditos
                    FROM compras as c
                    WHERE c.estado = "ACTIVO"
                    AND c.condicion_pago_id <> 1
                    AND YEAR(c.created_at) <= ?
                    AND MONTH(c.created_at) < ?', [$anio, $mes]);

        return  round($res[0]->creditos, 2);
    }

    public function getPagarAnt(string $anio, string $mes)
    {

        $consulta   =   DB::select('SELECT
                        IFNULL(SUM(ccd.monto),0) as pagar
                        FROM cuentas_proveedor_detalle AS ccd
                        WHERE
                        ccd.estado <> "ANULADO"
                        AND YEAR(ccd.created_at) <= ?
                        AND MONTH(ccd.created_at) < ?', [$anio, $mes]);

        return  round($consulta[0]->pagar, 2);
    }

    public function getCobranzaAnt(string $anio, string $mes)
    {
        //--ccd.status <> "ANULADO"

        $consulta   =   DB::select('SELECT
                        IFNULL(SUM(ccd.amount),0) as cobranza
                        FROM customer_accounts_details AS ccd
                        WHERE
                        YEAR(ccd.created_at) <= ?
                        AND MONTH(ccd.created_at) < ?', [$anio, $mes]);

        return  round($consulta[0]->cobranza, 2);
    }

    public function getCreditosCobranzaAnt(string $anio, string $mes): float
    {

        $res    =   DB::select('SELECT
                    IFNULL(SUM(v.total),0) as creditos
                    FROM sales_documents as v
                    WHERE v.status = "ACTIVO"
                    AND v.sunat_status NOT IN("ANULADO","ANULADO PARCIAL","BAJA")
                    AND v.payment_condition_id <> 1
                    AND YEAR(v.created_at) <= ?
                    AND MONTH(v.created_at) < ?', [$anio, $mes]);

        return  round($res[0]->creditos, 2);
    }

    public function excelDishMonth(array $data)
    {
        $company        =   Company::findOrFail(1);
        $anio           =   $data['year'];
        $month          =   $data['month'];
        $desde          =   Carbon::create($anio, $month, 1)->startOfMonth();
        $hasta          =   Carbon::create($anio, $month, 1)->endOfMonth();

        $report         =   $this->s_repository->queryDishesMonth($desde, $hasta);
        Carbon::setLocale('es');
        $monthName = strtoupper(Carbon::create()->month($month)->translatedFormat('F'));
        $data['month']  =   $monthName;
        return Excel::download(
            new DishesMonthExport($report, (object)$data, $company),
            'platos_mes_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx'
        );
    }

    public function excelProductsMonth(array $data)
    {
        $company        =   Company::findOrFail(1);
        $anio           =   $data['year'];
        $month          =   $data['month'];
        $desde          =   Carbon::create($anio, $month, 1)->startOfMonth();
        $hasta          =   Carbon::create($anio, $month, 1)->endOfMonth();

        $report         =   $this->s_repository->queryProductsMonth($desde, $hasta);
        Carbon::setLocale('es');
        $monthName = strtoupper(Carbon::create()->month($month)->translatedFormat('F'));
        $data['month']  =   $monthName;
        return Excel::download(
            new DishesMonthExport($report, (object)$data, $company),
            'productos_mes_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx'
        );
    }

    public function excelPaymentsMonth(array $data)
    {
        $company        =   Company::findOrFail(1);
        $anio           =   $data['year'];
        $month          =   $data['month'];
        $desde          =   Carbon::create($anio, $month, 1)->startOfMonth();
        $hasta          =   Carbon::create($anio, $month, 1)->endOfMonth();

        $report         =   $this->s_repository->queryPaymentMethodsMonth($desde, $hasta);
        Carbon::setLocale('es');
        $monthName = strtoupper(Carbon::create()->month($month)->translatedFormat('F'));
        $data['month']  =   $monthName;
        return Excel::download(
            new PaymentMethodMonthExport($report, (object)$data, $company),
            'metodos_pago_mes_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx'
        );
    }

    public function excelCostCenterMonth(array $data)
    {
        $company        =   Company::findOrFail(1);
        $anio           =   $data['year'];
        $month          =   $data['month'];
        $desde          =   Carbon::create($anio, $month, 1)->startOfMonth();
        $hasta          =   Carbon::create($anio, $month, 1)->endOfMonth();

        $report         =   $this->s_repository->queryCostCenterMonth($desde, $hasta);
        Carbon::setLocale('es');
        $monthName = strtoupper(Carbon::create()->month($month)->translatedFormat('F'));
        $data['month']  =   $monthName;
        return Excel::download(
            new CostCenterMonthExport($report, (object)$data, $company),
            'centro_costos_mes_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx'
        );
    }

    public function excelRankingWaiterMonth(array $data)
    {
        $company        =   Company::findOrFail(1);
        $anio           =   $data['year'];
        $month          =   $data['month'];
        $desde          =   Carbon::create($anio, $month, 1)->startOfMonth();
        $hasta          =   Carbon::create($anio, $month, 1)->endOfMonth();

        $report         =   $this->s_repository->queryRankingWaiterMonth($desde, $hasta);
        Carbon::setLocale('es');
        $monthName = strtoupper(Carbon::create()->month($month)->translatedFormat('F'));
        $data['month']  =   $monthName;
        return Excel::download(
            new CostCenterMonthExport($report, (object)$data, $company),
            'ranking_meseros_mes_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx'
        );
    }

    public function excelProductsStockMin(array $filters)
    {
        $data       =   $this->s_repository->getProductsStockMin($filters)->get();

        $company    =   Company::findOrFail(1);
        return Excel::download(new ProductoStockMinExport($data, $filters, $company), 'stock_minimo_' . Carbon::now()->format('Y-m-d') . '.xlsx');
    }
}
