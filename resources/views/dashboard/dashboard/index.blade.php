@extends('layouts.template')

@section('title')
    Dashboard
@endsection

@section('content')
    @csrf

    <div class="card-style settings-card-1 mb-3">
        <div class="card-title" style="font-size: 0.9rem; font-weight: bold; color: #1e3a8a; margin-bottom: 15px;">
            FILTRAR DATOS HISTÓRICOS
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-12 d-none">
                    <label for="select_establecimiento" style="font-weight:bold;">ESTABLECIMIENTO</label>
                    <select name="select_establecimiento" id="select_establecimiento">
                        <option selected value="MARKET">MARKET</option>
                        {{-- <option value="GRIFO">GRIFO</option> --}}
                    </select>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
                    <label for="filtro_anio" style="font-weight:bold;">AÑO</label>
                    <select id="filtro_anio" name="filtro_anio" class=""></select>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
                    <label for="filtro_mes" style="font-weight:bold;">MES</label>
                    <select id="filtro_mes" name="filtro_mes" class="form-control">
                    </select>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-12 d-flex align-items-end">
                    <button class="btn btn-primary" onclick="filtrarDatos();">
                        <i class="fas fa-filter"></i> FILTRAR
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3" style="height: auto;">
        <div class="card-body">

            <div class="row">
                <div class="col-12">
                    @include('dashboard.dashboard.assets.carousel')
                </div>
            </div>

        </div>
    </div>

    {{-- <div class="card" style="height: auto;">

        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="card-title"
                        style="font-size: 0.9rem; font-weight: bold; color: #1e3a8a; margin-bottom: 15px;">
                        ANALISIS DE RENTABILIDAD
                    </div>
                    <div class="table-responsive">
                        @include('dashboard.dashboard.tables.tbl_rentabilidad')
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="card-title"
                        style="font-size: 0.9rem; font-weight: bold; color: #1e3a8a; margin-bottom: 15px;">
                        ANALISIS TRIBUTARIO
                    </div>
                    <div class="table-responsive">
                        @include('dashboard.dashboard.tables.tbl_tributario')
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="card-title"
                        style="font-size: 0.9rem; font-weight: bold; color: #1e3a8a; margin-bottom: 15px;">
                        ANALISIS DE GESTIÓN DE EFICIENCIA
                    </div>
                    <div class="table-responsive">
                        @include('dashboard.dashboard.tables.tbl_eficiencia')
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="card-title"
                        style="font-size: 0.9rem; font-weight: bold; color: #1e3a8a; margin-bottom: 15px;">
                        ANALISIS DE GESTIÓN DE EXISTENCIAS
                    </div>
                    <div class="table-responsive">
                        @include('dashboard.dashboard.tables.tbl_existencias')
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="card mb-3" style="height: auto;">
        <div class="card-header">
            <h5>Estadísticas por mes</h5>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    @include('dashboard.dashboard.graficos.productos_mes')
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    @include('dashboard.dashboard.graficos.platos_mes')
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    @include('dashboard.dashboard.graficos.payment_method_month')
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    @include('dashboard.dashboard.graficos.cost_center_month')
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    @include('dashboard.dashboard.graficos.waiter_ranking_month')
                </div>
            </div>
        </div>
    </div>

    <div class="card" style="height: auto;">
        <div class="card-header">
            <h5>Estadísticas por año</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    @include('dashboard.dashboard.graficos.ventas_compras_anio')
                </div>

                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    @include('dashboard.dashboard.graficos.kpi_sales')
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3" style="height: auto;">
        <div class="card-title" style="font-size: 0.9rem; font-weight: bold; color: #1e3a8a; margin-bottom: 15px;">

        </div>
        <div class="card-body">
            <div class="row">
                {{-- <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    @include('dashboard.dashboard.graficos.cuentas_cobrar')
                </div> --}}
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    @include('dashboard.dashboard.graficos.cuentas_pagar')
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3" style="height: auto;">
        <div class="card-title" style="font-size: 0.9rem; font-weight: bold; color: #1e3a8a; margin-bottom: 15px;">

        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12" style="display:flex;justify-content:end;">
                    <button class="btn btn-primary" style="margin-right: 10px;" onclick="excelProductosStockMin();">
                        <i class="fas fa-file-excel"></i> EXCEL
                    </button>
                </div>
            </div>

            <div class="row">
                <label for="" style="font-weight: bold;">PRODUCTOS CON STOCK MIN PASADO</label>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="table-responsive">
                        @include('dashboard.dashboard.tables.tbl_stock_min')
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        cargarAnios();
        cargarMeses();
        loadSelectsDashboard();
        setAnioActual();
        setMesActual();
        filtrarDatos();
        iniciarDataTableProductos();
        dtCuentasCobrar = loadDataTableSimple('tbl_cuentas_cobrar');
        dtCuentasPagar = loadDataTableSimple('tbl_cuentas_pagar');
        events();
    })

    function events() {
        eventsDishesMonth();
        eventsProductsMonth();
        eventsPaymentMonth();
        eventsCostCenterMonth();
        eventsWaiterRankingMonth();
    }

    function removeCreditos() {
        const credits = document.querySelectorAll('.highcharts-credits');
        credits.forEach((c) => {
            c.textContent = '';
        })
    }

    async function filtrarDatos() {
        const establecimiento = document.querySelector('#select_establecimiento').value;
        const anio = document.querySelector('#filtro_anio').value;
        const mes = document.querySelector('#filtro_mes').value;

        if (!establecimiento || !anio || !mes) {
            toastr.error('FALTA SELECCIONAR FILTROS');
            return;
        }

        const res = await getData(establecimiento, anio, mes);
        if (!res) {
            return;
        }
        if (!res.success) {
            toastr.error(res.message, 'ERROR EN EL SERVIDOR');
        }

        toastr.info(res.message, 'OPERACIÓN COMPLETADA');
        pintarData(res.data);

        //====== REFRESCAR DATA TABLE PRODUCTOS =======
        destroyDataTable(dtProductos)
        clearTable('tbl_stock_min');
        iniciarDataTableProductos();
    }

    async function getData(establecimiento, anio, mes) {

        toastr.clear();
        const token = document.querySelector('input[name="_token"]').value;
        const routeGetData = @json(route('tenant.dashboard.dashboard.getData'));
        const url = `${routeGetData}?establecimiento=${establecimiento}&anio=${anio}&mes=${mes}`;

        try {
            mostrarAnimacion1();
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': token
                }
            });

            const res = await response.json();

            return res;

        } catch (error) {
            toastr.error(error, 'ERROR EN LA PETICIÓN OBTENER DATA');
            return null;
        } finally {
            ocultarAnimacion1();
        }

    }

    function pintarData(data) {
        pintarCarousel(data.data_carousel);
        pintarGraficos(data.data_graficos);
        // pintarAnalisis(data.data_analisis);
    }

    function pintarAnalisis(data) {

        //======= PINTAR TABLA RENTABILIDAD ======
        pintarTblRentabilidad(data.analisis_rentabilidad);

        //======= PINTAR TABLA TRIBUTARIO ======
        pintarTblTributario(data.analisis_tributario.data, data.analisis_tributario.renta);

        //====== PINTAR TABLA EFICIENCIA =======
        pintarTblEficiencia(data.analisis_eficiencia);

        //======== PINTAR TABLA EXISTENCIAS =======
        pintarTblExistencias(data.analisis_existencia);

    }

    function pintarCarousel(data) {
        const pUtilityTotal = document.querySelector('#utility_total');
        const pUtilityProducts = document.querySelector('#utility_products');
        const pUtilityDishes = document.querySelector('#utility_dishes');

        const pQuantityDishes = document.querySelector('#quantity_dishes');
        const pQuantityProducts = document.querySelector('#quantity_products');

        const pTotalInvoices = document.querySelector('#total_invoices');
        const pTotalBoletas = document.querySelector('#total_boletas');
        const pTotalFacturas = document.querySelector('#total_facturas');

        const pTotalSupplierAccounts = document.querySelector('#total_supplier_accounts');

        pUtilityTotal.textContent = formatSoles(data.utility_total);
        pUtilityProducts.textContent = formatSoles(data.utility_products);
        pUtilityDishes.textContent = formatSoles(data.utility_dishes);

        pQuantityDishes.textContent = data.quantity_dishes;
        pQuantityProducts.textContent = data.quantity_products;

        pTotalInvoices.textContent = formatSoles(data.total_invoices);
        pTotalBoletas.textContent = formatSoles(data.total_boletas);
        pTotalFacturas.textContent = formatSoles(data.total_facturas);

        pTotalSupplierAccounts.textContent = formatSoles(data.total_supplier_accounts);
    }

    function pintarGraficos(data) {

        setProductosMes(
            'PRODUCTOS MÁS VENDIDOS DEL MES',
            'En ventas no anuladas',
            data.productos
        );

        setPlatosMes(
            'PLATOS MÁS VENDIDOS DEL MES',
            'En ventas no anuladas',
            data.platos
        );

        setPaymentMethodMonth(
            'MÉTODOS PAGO DEL MES',
            '',
            data.payment_methods
        );

        setCostCenterMonth(
            'CENTRO COSTOS DEL MES',
            '',
            data.cost_center
        );

        setRankingWaiterMonth(
            'RANKING MESEROS MES',
            '',
            data.waiter_ranking
        );

        /*setCuentasCobrar(
            'CUENTAS POR COBRAR',
            '',
            data.cuentas_cobrar.cuentas,
            data.cuentas_cobrar.totales
        );*/

        setCuentasPagar(
            'CUENTAS POR PAGAR',
            '',
            data.cuentas_pagar.cuentas,
            data.cuentas_pagar.totales
        );

        setVentasVsComprasAnio(
            'VENTAS VS COMPRAS',
            'NIVEL DE VENTAS Y COMPRAS POR AÑO',
            data.ventas_vs_compras
        );

        setKpiSales('Kpi Ventas', 'ACTUAL VS ANTERIOR', data.kpi_ventas);
    }

    function getLang() {
        return {
            viewDataTable: "Ver tabla de datos",
            hideDataTable: "Ocultar tabla de datos",
            downloadPNG: "Descargar imagen PNG",
            downloadJPEG: "Descargar imagen JPEG",
            downloadPDF: "Descargar PDF",
            downloadSVG: "Descargar SVG",
            printChart: "Imprimir gráfico",
            viewFullscreen: "Ver en pantalla completa",
            downloadCSV: "Descargar CSV",
            downloadXLS: "Descargar Excel",
            openInCloud: "Abrir en Highcharts Cloud",
            category: "Categoría"
        };
    }

    function formatSoles(monto) {
        return new Intl.NumberFormat('es-PE', {
            style: 'currency',
            currency: 'PEN',
            minimumFractionDigits: 2
        }).format(monto);
    }


    function cargarMeses() {
        const meses = [
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];

        const selectMes = document.getElementById('filtro_mes');
        meses.forEach((mes, index) => {
            const option = document.createElement('option');
            option.value = index + 1;
            option.textContent = mes;
            selectMes.appendChild(option);
        });
    }

    function setMesActual() {
        const mesActual = new Date().getMonth() + 1;
        const selectMes = $('#filtro_mes');
        window.filterMesSelect.setValue(mesActual);
    }

    function cargarAnios() {
        const selectAnio = document.getElementById('filtro_anio');
        const anioActual = new Date().getFullYear();
        for (let year = 2025; year <= anioActual; year++) {
            const option = document.createElement('option');
            option.value = year;
            option.textContent = year;
            if (year === anioActual) {
                option.selected = true;
            }
            selectAnio.appendChild(option);
        }
    }

    function setAnioActual() {
        const anioActual = new Date().getFullYear();
        const selectAnio = $('#filtro_anio');
        selectAnio.val(anioActual).trigger('change');
    }

    function loadSelectsDashboard() {
        const filterAnioSelect = document.getElementById('filtro_anio');
        if (filterAnioSelect && !filterAnioSelect.tomselect) {
            window.filterAnioSelect = new TomSelect(filterAnioSelect, {
                valueField: 'id',
                labelField: 'description',
                searchField: ['description', 'id'],
                create: false,
                sortField: {
                    field: 'id',
                    direction: 'desc'
                },
                plugins: ['clear_button'],
                render: {
                    option: (item, escape) => `
                            <div>
                                ${escape(item.description)}
                            </div>
                        `,
                    item: (item, escape) => `
                            <div>${escape(item.description)}</div>
                        `
                }
            });
        }

        const filterMesSelect = document.getElementById('filtro_mes');
        if (filterMesSelect && !filterMesSelect.tomselect) {
            window.filterMesSelect = new TomSelect(filterMesSelect, {
                valueField: 'id',
                labelField: 'description',
                searchField: ['description', 'id'],
                create: false,
                plugins: ['clear_button'],
                render: {
                    option: (item, escape) => `
                            <div>
                                ${escape(item.description)}
                            </div>
                        `,
                    item: (item, escape) => `
                            <div>${escape(item.description)}</div>
                        `
                }
            });
        }
    }

    function excelProductosStockMin() {

        const url = @json(route('tenant.dashboard.dashboard.excelProductosStockMin'));

        const params = {
            establecimiento: document.querySelector('#select_establecimiento').value
        };

        const queryString = new URLSearchParams(params).toString();

        const finalUrl = `${url}?${queryString}`;
        window.location.href = finalUrl;

    }
</script>
