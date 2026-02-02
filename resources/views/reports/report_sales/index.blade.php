@extends('layouts.template')

@section('title')
    REPORTE DE VENTAS
@endsection

@section('content')
    <div class="card">
        @csrf
        <div class="card-header d-flex justify-content-between flex-row">
            <h4 class="card-title">REPORTE DE VENTAS</h4>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                    <label class="form-label fw-bold">Plato:</label>
                    <select class="form-control" id="dish_id" name="dish_id" required>
                        <option value="">Seleccionar</option>
                    </select>
                    <p class="dish_id_error msgError mb-0"></p>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                    <label class="form-label fw-bold">Producto:</label>
                    <select class="form-control" id="product_id" name="product_id" required>
                        <option value="">Seleccionar</option>
                    </select>
                    <p class="product_id_error msgError mb-0"></p>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
                    <label for="date_start" style="font-weight:bold;">FECHA INICIO</label>
                    <input type="date" class="form-control" id="date_start" value="{{ date('Y-m-01') }}">
                </div>

                <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
                    <label for="date_end" style="font-weight:bold;">FECHA FIN</label>
                    <input type="date" class="form-control" id="date_end" value="{{ date('Y-m-t') }}">
                </div>
                <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12" style="text-align: end;margin-top:auto;">
                    <button class="btn btn-primary btnFiltrar"><i class="fas fa-search"></i> FILTRAR</button>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-6 col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0 text-center">
                            <div class="mb-2">
                                <i class="fas fa-chart-line fa-2x text-success"></i>
                            </div>
                            <h6 class="card-title mb-1">
                                Total
                            </h6>
                            <h3 class="fw-bold text-success" id="total_sales">
                                S/ 0.00
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9 col-md-9 col-sm-6 col-12 text-end">
                    <button class="btn btn-success me-2" onclick="downloadExcel();">
                        <i class="fas fa-file-excel me-1"></i> Excel
                    </button>

                    <button class="btn btn-danger" onclick="downloadPdf();">
                        <i class="fas fa-file-pdf me-1"></i> PDF
                    </button>
                </div>
                <div class="col-12">
                    @include('reports.report_sales.tables.tbl_report_sales')
                </div>
            </div>
        </div>
    </div>
@endsection


@section('js')
    <script>
        let dtReportSale = null;

        document.addEventListener('DOMContentLoaded', () => {
            events();
            loadSelectRSales();
        })

        function events() {
            loadDtRSales();
            document.addEventListener('click', (e) => {
                if (e.target.closest('.btnFiltrar')) {
                    filtrar();
                }
            });
        }

        function loadDtRSales() {
            const url = '{{ route('tenant.reportes.ventas.getReporteVenta') }}';

            dtReportSale = new DataTable('#tbl_report_sales', {
                responsive: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: url,
                    type: 'GET',
                    data: function(d) {
                        d.dish_id = document.querySelector('#dish_id').value;
                        d.product_id = document.querySelector('#product_id').value;
                        d.date_start = document.querySelector('#date_start').value;
                        d.date_end = document.querySelector('#date_end').value;
                    },
                    beforeSend: function() {
                        mostrarAnimacion1();
                    },
                    complete: function() {
                        ocultarAnimacion1();
                    },
                    dataSrc: function(json) {
                        document.querySelector('#total_sales').textContent = formatSoles(json.total);
                        return json.data;
                    }
                },
                initComplete: function() {
                    const searchContainer = $('.dt-search');
                    const searchInput = $('#dt-search-0');

                    searchInput.attr('placeholder', 'Buscar ventas...');

                    if (!$('#dt-search-help').length) {
                        searchContainer.append(`
                        <div id="dt-search-help" class="text-muted mt-1 text-start" style="font-size: 0.85rem;">
                            Buscar documento, item.
                        </div>
                    `);
                    }
                },
                columns: [{
                        data: 'document',
                        name: 'document',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'item_name',
                        name: 'item_name',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'quantity',
                        name: 'quantity',
                        searchable: false,
                        orderable: true,
                        render: function(data, type, row) {
                            return formatSoles(data);
                        }
                    },
                    {
                        data: 'sale_price',
                        name: 'sale_price',
                        searchable: false,
                        orderable: true,
                        render: function(data, type, row) {
                            return formatSoles(data);
                        }
                    },
                    {
                        data: 'total',
                        name: 'total',
                        searchable: false,
                        orderable: true,
                        render: function(data, type, row) {
                            return formatSoles(data);
                        }
                    },
                ],
                pageLength: 50,
                lengthChange: false,
                language: {
                    "lengthMenu": "Mostrar _MENU_ registros por página",
                    "zeroRecords": "No se encontraron resultados",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                    "infoFiltered": "(filtrado de _MAX_ registros totales)",
                    "search": "Buscar:",
                    "paginate": {
                        "first": "Primero",
                        "last": "Último",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    },
                    "loadingRecords": "Cargando...",
                    "processing": "Procesando...",
                    "emptyTable": "No hay datos disponibles en la tabla",
                    "aria": {
                        "sortAscending": ": activar para ordenar la columna de manera ascendente",
                        "sortDescending": ": activar para ordenar la columna de manera descendente"
                    }
                }
            });
        }

        function loadSelectRSales() {
            window.dishSelect = new TomSelect('#dish_id', {
                valueField: 'id',
                labelField: 'text',
                searchField: ['text', 'subtext'],
                plugins: ['clear_button'],
                placeholder: 'Buscar plato,tipo plato',
                maxOptions: 20,
                create: false,
                preload: false,
                onType: (str) => {
                    lastCustomerQuery = str;
                },
                load: async (query, callback) => {
                    if (!query.length) return callback();
                    try {
                        const url = route('tenant.utils.searchDish', {
                            q: query,
                            warehouse_id: 1
                        });
                        const response = await fetch(url);
                        if (!response.ok) throw new Error('Error al buscar platos');
                        const data = await response.json();
                        const results = data.data ?? [];
                        callback(results);
                    } catch (error) {
                        console.error('Error cargando platos:', error);
                        callback();
                    }
                },
                render: {
                    option: (item, escape) => `
                        <div>
                            <strong>${escape(item.text)}</strong><br>
                            <small>${escape(item.subtext ?? '')}</small>
                        </div>
                    `,
                    item: (item, escape) => `<div>${escape(item.text)}</div>`
                }
            });
            window.productSelect = new TomSelect('#product_id', {
                valueField: 'id',
                labelField: 'name',
                searchField: ['name', 'category_name', 'brand_name'],
                plugins: ['clear_button'],
                placeholder: 'Buscar producto,categoría,marca',
                maxOptions: 20,
                create: false,
                preload: false,
                onType: (str) => {
                    lastCustomerQuery = str;
                },
                load: async (query, callback) => {
                    if (!query.length) return callback();
                    try {
                        const url = route('tenant.utils.searchProduct', {
                            q: query,
                            warehouse_id: 1
                        });
                        const response = await fetch(url);
                        if (!response.ok) throw new Error('Error al buscar clientes');
                        const data = await response.json();
                        const results = data.data ?? [];
                        callback(results);
                    } catch (error) {
                        console.error('Error cargando clientes:', error);
                        callback();
                    }
                },
                render: {
                    option: (item, escape) => `
                        <div>
                            <strong>${escape(item.name)}</strong><br>
                            <small>${escape(item.subtext ?? '')}</small>
                        </div>
                    `,
                    item: (item, escape) => `<div>${escape(item.name)}</div>`
                }
            });
        }

        function goToSaleCreate() {
            const route = @json(route('tenant.ventas.comprobante_venta.create'));
            window.location.href = route;
        }

        function filterDataTable() {
            dtReportSale.ajax.reload();
        }

        function downloadExcel() {

            const url = @json(route('tenant.reportes.ventas.excel'));

            const params = {
                date_start: document.querySelector('#date_start').value,
                date_end: document.querySelector('#date_end').value,
                dish_id: document.querySelector('#dish_id').value,
                product_id: document.querySelector('#product_id').value
            };

            const queryString = new URLSearchParams(params).toString();

            const finalUrl = `${url}?${queryString}`;
            window.location.href = finalUrl;

        }

        function downloadPdf() {

            const url = @json(route('tenant.reportes.ventas.pdf'));

            const params = {
                date_start: document.querySelector('#date_start').value,
                date_end: document.querySelector('#date_end').value,
                dish_id: document.querySelector('#dish_id').value,
                product_id: document.querySelector('#product_id').value
            };

            const queryString = new URLSearchParams(params).toString();

            const finalUrl = `${url}?${queryString}`;
            window.open(finalUrl, '_blank');

        }

        function filtrar() {
            toastr.clear();
            const fecha_inicio = document.querySelector('#date_start').value;
            const fecha_fin = document.querySelector('#date_end').value;

            if (fecha_inicio > fecha_fin && fecha_fin && fecha_inicio) {
                toastr.error('LA FECHA DE INICIO DEBE SER MENOR IGUAL A LA FECHA FINAL!!');
                document.querySelector('#fecha_inicio').focus();
                return;
            }
            dtReportSale.draw();
        }
    </script>
@endsection
