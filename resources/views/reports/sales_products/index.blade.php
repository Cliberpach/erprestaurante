@extends('layouts.template')
@section('title')
    VENTAS - PRODUCTOS
@endsection

@section('content')
    <div class="card overflow-hidden">
        <div class="card-header">

            <!-- Fila 1: Título + Botón -->
            <div class="row align-items-center mb-3">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <h6 class="card-title mb-0">Reporte Venta Producto</h6>
                </div>
            </div>

            <div class="row mb-3">

                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                    <label class="form-label fw-bold">Producto:</label>
                    <select class="form-control" id="product_id" name="product_id" required>
                        <option value="">Seleccionar</option>
                    </select>
                    <p class="product_id_error msgError mb-0"></p>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
                    <label for="date_start" style="font-weight:bold;">FECHA INICIO</label>
                    <input type="date" class="form-control" id="fecha_inicio" value="{{ date('Y-m-01') }}">
                </div>
                <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
                    <label for="date_end" style="font-weight:bold;">FECHA FIN</label>
                    <input type="date" class="form-control" id="fecha_fin" value="{{ date('Y-m-t') }}">
                </div>
                <div class="col-lg-5 col-md-3 col-sm-12 col-xs-12" style="text-align: end;margin-top:auto;">
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
                                Utilidad Total
                            </h6>
                            <h3 class="fw-bold text-success" id="utility_total">
                                S/ 0.00
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9 col-md-9 col-sm-6 col-12 d-flex align-items-start justify-content-end">
                    <button class="btn btn-success" style="margin-right: 10px;" onclick="downloadExcel();">
                        <i class="fas fa-file-excel"></i> EXCEL
                    </button>
                    <button class="btn btn-danger" onclick="downloadPdf()">
                        <i class="fas fa-file-pdf"></i> PDF
                    </button>
                </div>
            </div>

        </div>
        <div class="card-body p-0 pb-2">
            @include('reports.sales_products.tables.tbl_list')
        </div>
    </div>
@endsection

@section('js')
    <script>
        let dtQuery = null;

        document.addEventListener('DOMContentLoaded', () => {
            loadtDtQuery();
            loadTomSelect();
            events();
        })

        function events() {
            document.addEventListener('click', (e) => {
                if (e.target.closest('.btnFiltrar')) {
                    filtrar();
                }
            });
        }

        function loadTomSelect() {
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

        function loadtDtQuery() {
            const url = '{{ route('tenant.reportes.ventas_productos.getAll') }}';

            dtQuery = new DataTable('#tbl_list', {
                serverSide: true,
                processing: true,
                pageLength: 50,
                responsive: true,
                ajax: {
                    url: url,
                    type: 'GET',
                    data: function(d) {
                        d.start_date = document.querySelector('#fecha_inicio').value;
                        d.end_date = document.querySelector('#fecha_fin').value;
                        d.product_id = document.querySelector('#product_id').value;
                    },
                    beforeSend: function() {
                        mostrarAnimacion1();
                    },
                    complete: function() {
                        ocultarAnimacion1();
                    },
                    dataSrc: function(json) {
                        document.querySelector('#utility_total').textContent = formatSoles(json.utility_total);
                        return json.data;
                    }
                },
                initComplete: function() {
                    const input = $('#dt-search-0');

                    if (!$('#search-help').length) {
                        input.after(`
                            <small id="search-help" class="text-black d-block mt-1 text-start">
                                Buscar:
                                <strong>Producto</strong>
                            </small>
                        `);
                    }
                },
                columns: [{
                        data: 'product_id',
                        name: 'sp.product_id',
                        visible: false,
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'quantity',
                        name: 'sp.quantity',
                        searchable: false,
                        orderable: true
                    },
                    {
                        data: 'product_name',
                        name: 'sp.product_name',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'category_name',
                        name: 'sp.category_name',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'brand_name',
                        name: 'sp.brand_name',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'sale_price',
                        name: 'sp.sale_price',
                        searchable: false,
                        orderable: true,
                        render: function(data, type, row) {
                            return formatSoles(data);
                        }
                    },
                    {
                        data: 'purchase_price',
                        name: 'p.purchase_price',
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
                    {
                        data: 'purchase_total',
                        name: 'purchase_total',
                        searchable: false,
                        orderable: true,
                        render: function(data, type, row) {
                            return formatSoles(data);
                        }
                    },
                    {
                        data: 'utility',
                        name: 'utility',
                        searchable: false,
                        orderable: true,
                        className: "text-end",
                        render: function(data, type, row) {
                            return formatSoles(data);
                        }
                    }
                ],
                language: {
                    "lengthMenu": "Mostrar _MENU_ items por página",
                    "zeroRecords": "No se encontraron resultados",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ items",
                    "infoEmpty": "Mostrando 0 a 0 de 0 items",
                    "infoFiltered": "(filtrado de _MAX_ items totales)",
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

        function filterDataTable() {
            dtQuery.ajax.reload();
        }

        function downloadExcel() {

            const url = @json(route('tenant.reportes.ventas_productos.excel'));

            const params = {
                start_date: document.querySelector('#fecha_inicio').value,
                end_date: document.querySelector('#fecha_fin').value,
                product_id: document.querySelector('#product_id').value,
            };

            const queryString = new URLSearchParams(params).toString();

            const finalUrl = `${url}?${queryString}`;
            window.location.href = finalUrl;

        }

        function downloadPdf() {

            const url = @json(route('tenant.reportes.ventas_productos.pdf'));

            const params = {
                start_date: document.querySelector('#fecha_inicio').value,
                end_date: document.querySelector('#fecha_fin').value,
                product_id: document.querySelector('#product_id').value,
            };

            const queryString = new URLSearchParams(params).toString();

            const finalUrl = `${url}?${queryString}`;
            window.open(finalUrl, '_blank');

        }

        function filtrar() {
            toastr.clear();
            const fecha_inicio = document.querySelector('#fecha_inicio').value;
            const fecha_fin = document.querySelector('#fecha_fin').value;

            if (fecha_inicio > fecha_fin && fecha_fin && fecha_inicio) {
                toastr.error('LA FECHA DE INICIO DEBE SER MENOR IGUAL A LA FECHA FINAL!!');
                document.querySelector('#fecha_inicio').focus();
                return;
            }
            dtQuery.draw();
        }
    </script>
@endsection
