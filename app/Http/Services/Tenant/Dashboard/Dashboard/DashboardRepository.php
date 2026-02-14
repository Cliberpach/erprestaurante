<?php

namespace App\Http\Services\Tenant\Dashboard\Dashboard;

use App\Models\Tenant\Accounts\SupplierAccount\SupplierAccount;
use Illuminate\Support\Facades\DB;

class DashboardRepository
{
    public function queryDishesMonth(string $desde, string $hasta)
    {
        $dishes  =   DB::table('sales_dishes as sd')
            ->join('sales as s', 's.id', '=', 'sd.sale_id')
            ->join('dishes as d', 'd.id', '=', 'sd.dish_id')
            ->select(
                'd.id',
                'd.name',
                DB::raw('SUM(sd.quantity) as quantity'),
                'sd.sale_price',
                DB::raw('SUM(sd.total) AS total')
            )
            ->where('sd.status', '<>', 'ANULADO')
            ->whereNotIn('s.status', ['ANULADO', 'BAJA'])
            ->whereBetween('s.created_at', [$desde, $hasta])
            ->groupBy('d.id', 'd.name', 'sd.sale_price')
            ->orderByDesc('total')
            ->get();

        return $dishes;
    }

    public function queryProductsMonth(string $desde, string $hasta)
    {
        $dishes  =   DB::table('sales_products as sp')
            ->join('sales as s', 's.id', '=', 'sp.sale_id')
            ->join('products as p', 'p.id', '=', 'sp.product_id')
            ->select(
                'p.id',
                'p.name',
                DB::raw('SUM(sp.quantity) as quantity'),
                'sp.sale_price',
                DB::raw('SUM(sp.total) AS total')
            )
            ->where('sp.status', '<>', 'ANULADO')
            ->whereNotIn('s.status', ['ANULADO', 'BAJA'])
            ->whereBetween('s.created_at', [$desde, $hasta])
            ->groupBy('p.id', 'p.name', 'sp.sale_price')
            ->orderByDesc('total')
            ->get();

        return $dishes;
    }

    public function queryPaymentMethodsMonth(string $desde, string $hasta)
    {
        $items = DB::table('payment_methods as pm')
            ->leftJoin('sales_pays as sp', function ($join) use ($desde, $hasta) {
                $join->on('pm.id', '=', 'sp.payment_method_id')
                    ->whereBetween('sp.created_at', [$desde, $hasta]);
            })
            ->select(
                'pm.id',
                'pm.description as payment_method_name'
            )
            ->selectRaw('COALESCE(SUM(sp.amount), 0) as amount')
            ->groupBy('pm.id', 'pm.description')
            ->orderByDesc('amount')
            ->get();

        return  $items;
    }

    public function queryCostCenterMonth(string $desde, string $hasta)
    {
        $cost_center  =   DB::table('cost_center as cc')
            ->join('exit_money as em', 'em.cost_center_id', '=', 'cc.id')
            ->select(
                'cc.id',
                'cc.name',
                DB::raw('SUM(em.total) as amount')
            )
            ->whereBetween('em.created_at', [$desde, $hasta])
            ->groupBy('cc.id', 'cc.name')
            ->orderByDesc('amount')
            ->get();

        return $cost_center;
    }

    public function queryRankingWaiterMonth(string $desde, string $hasta)
    {
        $items = DB::table('users as u')
            ->join('model_has_roles as mhr', 'mhr.model_id', 'u.id')
            ->leftJoin('orders as o', function ($join) use ($desde, $hasta) {
                $join->on('o.creator_user_id', '=', 'u.id')
                    ->whereBetween('o.created_at', [$desde, $hasta]);
            })
            ->leftJoin('sales as s', function ($join) use ($desde, $hasta) {
                $join->on('s.order_id', '=', 'o.id')
                    ->whereNotIn('s.sunat_status', ["ANULADO", 'ANULADO PARCIAL']);
            })
            ->select(
                'u.id',
                'u.name',
            )
            ->selectRaw('COALESCE(SUM(s.total), 0) as amount')
            ->where('u.status', 'ACTIVO')
            ->where('mhr.role_id', 2)
            ->groupBy('u.id', 'u.name')
            ->orderByDesc('amount')
            ->get();

        return $items;
    }

    public function getSalesCarousel($desde, $hasta)
    {
        $report = DB::selectOne(
            'WITH filtered_sales AS (
                SELECT id, total, type_sale_code
                FROM sales
                WHERE created_at BETWEEN ? AND ?
            )

            SELECT
                /* UTILIDADES */
                (SELECT COALESCE(SUM((sp.sale_price - p.purchase_price)*sp.quantity) ,0)
                FROM filtered_sales fs
                JOIN sales_products sp ON sp.sale_id = fs.id
                JOIN products p ON p.id = sp.product_id
                WHERE sp.status <> "ANULADO"
                ) AS utility_products,

                (SELECT COALESCE(SUM((sd.sale_price - d.purchase_price)*sd.quantity) * sd.quantity,0)
                FROM filtered_sales fs
                JOIN sales_dishes sd ON sd.sale_id = fs.id
                JOIN dishes d ON d.id = sd.dish_id
                WHERE sd.status <> "ANULADO"
                ) AS utility_dishes,

                /* TOTALES */
                (SELECT COALESCE(SUM(total),0)
                FROM filtered_sales
                WHERE type_sale_code = "03"
                ) AS total_boletas,

                (SELECT COALESCE(SUM(total),0)
                FROM filtered_sales
                WHERE type_sale_code = "01"
                ) AS total_facturas,

                (SELECT COALESCE(SUM(total),0)
                FROM filtered_sales
                WHERE type_sale_code IN ("03","01")
                ) AS total_invoices,

                /* CANTIDADES */
                (SELECT SUM(sp.quantity) FROM sales_products sp
                JOIN filtered_sales fs ON fs.id = sp.sale_id
                WHERE sp.status <> "ANULADO"
                ) AS quantity_products,

                (SELECT SUM(sd.quantity) FROM sales_dishes sd
                JOIN filtered_sales fs ON fs.id = sd.sale_id
                WHERE sd.status <> "ANULADO"
                ) AS quantity_dishes
            ',
            [$desde, $hasta]
        );

        return $report;
    }

    public function getSupplierAccountsCarousel($desde, $hasta)
    {
        $total  =   SupplierAccount::sum('amount');
        return $total;
    }

    public function getProductosMes(string $desde, string $hasta)
    {
        $productos  =   DB::table('sales_products as sp')
            ->join('sales as s', 's.id', '=', 'sp.sale_id')
            ->join('products as p', 'p.id', '=', 'sp.product_id')
            ->select(
                'p.id',
                'p.name',
                DB::raw('SUM(sp.quantity) as cantidad_vendida'),
            )
            ->where('sp.status', '<>', 'ANULADO')
            ->whereNotIn('s.status', ['ANULADO', 'BAJA'])
            ->whereBetween('s.created_at', [$desde, $hasta])
            ->groupBy('p.id', 'p.name')
            ->orderByDesc('cantidad_vendida')
            ->limit(10)
            ->get();

        $formateado = $productos->map(function ($item) {
            return [$item->name, (float) $item->cantidad_vendida];
        });

        return  $formateado;
    }

    public function getPlatosMes(string $desde, string $hasta)
    {
        $platos  =   DB::table('sales_dishes as sd')
            ->join('sales as s', 's.id', '=', 'sd.sale_id')
            ->join('dishes as d', 'd.id', '=', 'sd.dish_id')
            ->select(
                'd.id',
                'd.name',
                DB::raw('SUM(sd.quantity) as cantidad_vendida')
            )
            ->where('sd.status', '<>', 'ANULADO')
            ->whereNotIn('s.status', ['ANULADO', 'BAJA'])
            ->whereBetween('s.created_at', [$desde, $hasta])
            ->groupBy('d.id', 'd.name')
            ->orderByDesc('cantidad_vendida')
            ->limit(10)
            ->get();

        $formateado = $platos->map(function ($item) {
            return [$item->name, (float) $item->cantidad_vendida];
        });

        return  $formateado;
    }

    public function getCostCenterMonth(string $desde, string $hasta)
    {
        $cost_center  =   DB::table('cost_center as cc')
            ->join('exit_money as em', 'em.cost_center_id', '=', 'cc.id')
            ->select(
                'cc.id',
                'cc.name',
                DB::raw('SUM(em.total) as amount')
            )
            ->whereBetween('em.created_at', [$desde, $hasta])
            ->groupBy('cc.id', 'cc.name')
            ->orderByDesc('amount')
            ->limit(10)
            ->get();

        $formateado = $cost_center->map(function ($item) {
            return [$item->name, (float) $item->amount];
        });

        return  $formateado;
    }

    public function getWaiterRankingMonth(string $desde, string $hasta)
    {
        $items = DB::table('users as u')
            ->join('model_has_roles as mhr', 'mhr.model_id', 'u.id')
            ->leftJoin('orders as o', function ($join) use ($desde, $hasta) {
                $join->on('o.creator_user_id', '=', 'u.id')
                    ->whereBetween('o.created_at', [$desde, $hasta]);
            })
            ->leftJoin('sales as s', function ($join) use ($desde, $hasta) {
                $join->on('s.order_id', '=', 'o.id')
                    ->whereNotIn('s.sunat_status', ["ANULADO", 'ANULADO PARCIAL']);
            })
            ->select(
                'u.id',
                'u.name',
            )
            ->selectRaw('COALESCE(SUM(s.total), 0) as amount')
            ->where('u.status', 'ACTIVO')
            ->where('mhr.role_id', 2)
            ->groupBy('u.id', 'u.name')
            ->orderByDesc('amount')
            ->limit(10)
            ->get();

        $amounts = $items->pluck('amount')->map(fn($v) => (float) $v);
        $names   = $items->pluck('name');

        return  ['names' => $names, 'amounts' => $amounts];
    }

    public function getPaymentMethodMonth(string $desde, string $hasta)
    {
        $items = DB::table('payment_methods as p')
            ->leftJoin('sales_pays as sp', function ($join) use ($desde, $hasta) {
                $join->on('sp.payment_method_id', '=', 'p.id')
                    ->whereBetween('sp.created_at', [$desde, $hasta]);
            })
            ->select(
                'p.id',
                'p.description as payment_method_name'
            )
            ->selectRaw('COALESCE(SUM(sp.amount), 0) as amount')
            ->groupBy('p.id', 'p.description')
            ->orderByDesc('amount')
            ->limit(10)
            ->get();

        $formateado = $items->map(function ($item) {
            return [$item->payment_method_name, (float) $item->amount];
        });

        return  $formateado;
    }

    public function getCuentasPagar(): object
    {

        $cuentas_proveedores    =   DB::select('SELECT
                                    CONCAT(s.type_document_abbreviation,":",s.document_number,"-",s.name) as proveedor_nombre,
                                    round(SUM(sa.balance),2) as proveedor_saldo
                                    from supplier_accounts as sa
                                    JOIN purchase_documents AS pd ON pd.id = sa.purchase_id
                                    JOIN suppliers as s on s.id = pd.supplier_id
                                    GROUP BY s.type_document_abbreviation,s.document_number,s.name');

        $cuentas_proveedores_array  =   [];
        foreach ($cuentas_proveedores as $cuenta) {
            $cuentas_proveedores_array[]    =   [
                $cuenta->proveedor_nombre,
                round((float) $cuenta->proveedor_saldo, 2)
            ];
        }

        $totales        =   DB::select('SELECT
                                ROUND(IFNULL(SUM(cpg.balance), 0),2) AS pendiente,
                                ROUND(IFNULL(SUM(cpg.paid), 0),2) AS cobrado,
                                ROUND(IFNULL(SUM(cpg.amount), 0), 2) AS total
                            FROM supplier_accounts AS cpg
                            ');

        $resultado  =   (object)['cuentas' => $cuentas_proveedores_array, 'totales' => $totales[0]];

        return $resultado;
    }

    public function getProductsStockMin($filters)
    {
        $categoria_id       =   $filters['categoria_id'] ?? null;
        $marca_id           =   $filters['marca_id'] ?? null;
        $establecimiento    =   $filters['establecimiento'] ?? null;

        $productos = DB::table('products as p')
            ->join('brands as m', 'm.id', '=', 'p.brand_id')
            ->join('categories as c', 'c.id', '=', 'p.category_id')
            ->join('warehouse_products as ap', 'ap.product_id', 'p.id')
            ->select(
                'p.id',
                'p.brand_id',
                'p.category_id',
                'p.unit_id',
                'p.name',
                'p.code_bar',
                'p.sale_price',
                'p.purchase_price',
                'p.stock_min',
                'm.name as brand_name',
                'c.name as category_name',
                'p.unit_symbol AS unit_measurement',
                'p.unit_symbol',
                'p.status',
                'ap.stock'
            )
            ->where('p.status', 'ACTIVO')
            ->where('ap.warehouse_id', 1)
            ->whereColumn('ap.stock', '<', 'p.stock_min');


        if ($categoria_id) {
            $productos  =   $productos->where('p.category_id', $categoria_id);
        }

        if ($marca_id) {
            $productos  =   $productos->where('p.brand_id', $marca_id);
        }

        return $productos;
    }

    public function getVentasVsComprasAnio(string $year): array
    {

        DB::statement("SET lc_time_names = 'es_ES'");

        $ventas =   DB::table('sales as s')
            ->select(
                DB::raw('MONTH(s.created_at) as mes'),
                DB::raw('UPPER(MONTHNAME(s.created_at)) as nombre_mes'),
                DB::raw('SUM(s.total) as total_mes')
            )
            ->where('s.status', 'ACTIVO')
            ->whereYear('s.created_at', $year)
            ->whereNotIn('s.sunat_status', ['ANULADO', 'ANULADO PARCIAL', 'BAJA'])
            ->groupBy(DB::raw('MONTH(s.created_at)'), DB::raw('UPPER(MONTHNAME(s.created_at))'))
            ->orderBy('mes')
            ->get();

        $todos_los_meses = [
            1 => 'ENERO',
            2 => 'FEBRERO',
            3 => 'MARZO',
            4 => 'ABRIL',
            5 => 'MAYO',
            6 => 'JUNIO',
            7 => 'JULIO',
            8 => 'AGOSTO',
            9 => 'SEPTIEMBRE',
            10 => 'OCTUBRE',
            11 => 'NOVIEMBRE',
            12 => 'DICIEMBRE'
        ];

        $ventas_por_mes = array_fill(1, 12, 0);

        foreach ($ventas as $venta) {
            $ventas_por_mes[$venta->mes] = $venta->total_mes;
        }

        $res_ventas = [];
        foreach ($todos_los_meses as $mes_num => $mes_nombre) {
            $res_ventas[]   =   round($ventas_por_mes[$mes_num], 2);
        }


        //======== COMPRAS ==========
        $compras =   DB::table('purchase_documents as c')
            ->select(
                DB::raw('MONTH(c.created_at) as mes'),
                DB::raw('UPPER(MONTHNAME(c.created_at)) as nombre_mes'),
                DB::raw('SUM(c.total) as total_mes')
            )
            ->where('c.status', 'ACTIVO')
            ->whereYear('c.created_at', $year)
            ->groupBy(DB::raw('MONTH(c.created_at)'), DB::raw('UPPER(MONTHNAME(c.created_at))'))
            ->orderBy('mes')
            ->get();


        $compras_por_mes = array_fill(1, 12, 0);

        foreach ($compras as $compra) {
            $compras_por_mes[$compra->mes] = $compra->total_mes;
        }


        $res_compras = [];
        foreach ($todos_los_meses as $mes_num => $mes_nombre) {
            $res_compras[]   =   round($compras_por_mes[$mes_num], 2);
        }

        $resultados['ventas']       =   $res_ventas;
        $resultados['compras']      =   $res_compras;

        return $resultados;
    }

    public function kpiDayMonthYear()
    {
        $kpiVentas = DB::selectOne('SELECT

            COALESCE(SUM(CASE WHEN DATE(created_at) = CURDATE() THEN total END),0) AS today_sales,

            COALESCE(SUM(CASE
                WHEN DATE(created_at) = CURDATE() - INTERVAL 1 DAY
                THEN total END),0) AS sales_yesterday,

            COALESCE(SUM(CASE
                WHEN YEAR(created_at) = YEAR(CURDATE())
                AND MONTH(created_at) = MONTH(CURDATE())
                THEN total END),0) AS sales_month,

            COALESCE(SUM(CASE
                WHEN YEAR(created_at) = YEAR(CURDATE() - INTERVAL 1 MONTH)
                AND MONTH(created_at) = MONTH(CURDATE() - INTERVAL 1 MONTH)
                THEN total END),0) AS sales_previous_month,

            COALESCE(SUM(CASE
                WHEN YEAR(created_at) = YEAR(CURDATE())
                THEN total END),0) AS sales_year,

            COALESCE(SUM(CASE
                WHEN YEAR(created_at) = YEAR(CURDATE()) - 1
                THEN total END),0) AS sales_previous_year

            FROM sales s
            WHERE s.sunat_status NOT IN("ANULADO","BAJA")
        ');

        return $kpiVentas;
    }
}
