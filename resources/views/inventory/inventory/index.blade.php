@extends('layouts.template')

@section('title')
    INVENTARIO
@endsection

@section('content')
    <div class="card">
        @csrf
        <div class="card-header d-flex justify-content-between flex-row">
            <h4 class="card-title">INVENTARIO</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <label for="filter_stock" style="font-weight:bold;">STOCK</label>
                    <select data-placeholder="Seleccionar" id="filter_stock" class="select2_form form-select"
                        aria-label="Default select example" onchange="filterDataTable()">
                        <option value="1">TODOS</option>
                        <option value="2">STOCK = 0</option>
                        <option value="3">STOCK > 0</option>
                        <option value="4">MENOR QUE STOCK MÍNIMO</option>

                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-12" style="display:flex;justify-content:end;">
                    <button class="btn btn-primary" style="margin-right: 10px;" onclick="downloadExcel();">EXCEL</button>
                    <button class="btn btn-primary" onclick="downloadPdf();">PDF</button>
                </div>
                <div class="col-12">
                    @include('inventory.inventory.tables.tbl_list_inventory')
                </div>
            </div>

        </div>
    </div>
@endsection

@section('js')
    <script>
        let dtInventory = null;

        document.addEventListener('DOMContentLoaded', () => {
            events();
        })

        function events() {
            startDataTableInventory();
            loadSelectInventory();
        }

        function loadSelectInventory() {
            const filterStockSelect = document.getElementById('filter_stock');
            if (filterStockSelect && !filterStockSelect.tomselect) {
                window.filterStockSelect = new TomSelect(filterStockSelect, {
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
        }

        function startDataTableInventory() {
            const urlGetInventory = '{{ route('tenant.inventario.inventario.getInventory') }}';

            dtInventory = new DataTable('#tbl_list_inventory', {
                responsive: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: urlGetInventory,
                    type: 'GET',
                    data: function(d) {
                        d.filter_stock = document.querySelector('#filter_stock').value;
                    }
                },
                order: [
                    [0, 'desc']
                ],
                columns: [{
                        data: 'id',
                        name: 'id',
                        visible: false
                    },
                    {
                        data: 'product_name',
                        name: 'product_name'
                    },
                    {
                        data: 'category_name',
                        name: 'category_name'
                    },
                    {
                        data: 'brand_name',
                        name: 'brand_name'
                    },
                    {
                        data: 'stock_min',
                        name: 'stock_min'
                    },
                    {
                        data: 'current_stock',
                        name: 'current_stock'
                    },
                    {
                        data: 'sale_price',
                        name: 'sale_price'
                    },
                    {
                        data: 'purchase_price',
                        name: 'purchase_price'
                    }
                ],
                pageLength: 25,
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


        function goToSaleCreate() {
            const route = @json(route('tenant.ventas.comprobante_venta.create'));
            window.location.href = route;
        }

        function filterDataTable() {
            dtInventory.ajax.reload();
        }

        function changeDateStart(date_start) {

            toastr.clear();
            const date_end = document.querySelector('#date_end').value;

            if (date_start > date_end && date_end) {
                document.querySelector('#date_start').value = '';
                toastr.error('LA FECHA DE INICIO DEBE SER MENOR IGUAL A LA FECHA FINAL!!');
                return;
            }

            filterDataTable();

        }

        function changeDateEnd(date_end) {

            toastr.clear();
            const date_start = document.querySelector('#date_start').value;

            if (date_end < date_start && date_start) {
                document.querySelector('#date_end').value = '';
                toastr.error('LA FECHA FINAL DEBE SER MAYOR IGUAL A LA FECHA INICIAL!!');
                return;
            }

            filterDataTable();

        }


        function downloadExcel() {

            const url = @json(route('tenant.inventario.inventario.excel'));

            const params = {
                filter_stock: document.querySelector('#filter_stock').value
            };

            const queryString = new URLSearchParams(params).toString();

            const finalUrl = `${url}?${queryString}`;
            window.location.href = finalUrl;

        }

        function downloadPdf() {

            const url = @json(route('tenant.inventario.inventario.pdf'));

            const params = {
                filter_stock: document.querySelector('#filter_stock').value
            };

            const queryString = new URLSearchParams(params).toString();

            const finalUrl = `${url}?${queryString}`;
            window.open(finalUrl, '_blank');

        }
    </script>
    <script src="{{ asset('assets/js/utils.js') }}"></script>
@endsection
