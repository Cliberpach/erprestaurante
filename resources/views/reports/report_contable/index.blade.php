@extends('layouts.template')

@section('title')
    REPORTE CONTABLE
@endsection

@section('content')
    <div class="card">
        @csrf
        <div class="card-header d-flex justify-content-between flex-row">
            <h4 class="card-title">REPORTE CONTABLE</h4>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <label for="filter_document" style="font-weight:bold;">DOCUMENTO</label>
                    <select data-placeholder="Seleccionar" id="filter_document" class="form-select"
                        aria-label="Default select example">
                        <option value=""></option>
                        <option value="VENTAS">VENTAS (FAC,BOL,NOTA CRED)</option>
                        @foreach ($invoices as $invoice)
                            <option value="{{ $invoice->id }}">{{ $invoice->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
                    <label for="date_start" style="font-weight:bold;">FECHA INICIO</label>
                    <input type="date" class="form-control" id="date_start">
                </div>
                <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
                    <label for="date_end" style="font-weight:bold;">FECHA FIN</label>
                    <input type="date" class="form-control" id="date_end">
                </div>
                <div class="col-lg-4 col-md-2 col-sm-12 col-xs-12" style="text-align: end;margin-top:auto;">
                    <button class="btn btn-primary btnFiltrar"><i class="fas fa-search"></i> FILTRAR</button>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-end">
                    <button class="btn btn-success me-2" onclick="downloadExcel();">
                        <i class="fas fa-file-excel me-1"></i> Excel
                    </button>

                    <button class="btn btn-danger" onclick="downloadPdf();">
                        <i class="fas fa-file-pdf me-1"></i> PDF
                    </button>
                </div>
                <div class="col-12">
                    <div class="table-responsive">
                        @include('reports.report_contable.tables.tbl_report_contable')
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('js')
    <script>
        let dtContable = null;

        document.addEventListener('DOMContentLoaded', () => {
            events();
        })

        function events() {
            loadDtContable();
            loadTomSelect();
            document.addEventListener('click', (e) => {
                if (e.target.closest('.btnFiltrar')) {
                    filtrar();
                }
            });
        }

        function loadTomSelect() {
            const invoiceSelect = document.getElementById('filter_document');
            if (invoiceSelect && !invoiceSelect.tomselect) {
                window.invoiceSelect = new TomSelect(invoiceSelect, {
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

        function loadDtContable() {
            const urlGetReportContable = '{{ route('tenant.reportes.contable.getAll') }}';

            dtContable = new DataTable('#tbl_report_contable', {
                responsive: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: urlGetReportContable,
                    type: 'GET',
                    data: function(d) {
                        d.date_start = document.querySelector('#date_start').value;
                        d.date_end = document.querySelector('#date_end').value;
                        d.filter_document = document.querySelector('#filter_document').value;
                    }
                },
                order: [
                    [4, 'desc']
                ],
                columns: [{
                        data: 'created_at',
                        name: 's.created_at',
                        searchable: false,
                        orderable: true
                    },
                    {
                        data: 'type_sale_name',
                        name: 's.type_sale_name',
                        searchable: true,
                        orderable: false
                    },
                    {
                        data: 'type_sale_code',
                        name: 's.type_sale_code',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'serie',
                        name: 's.serie',
                        serchable: true,
                        orderable: true
                    },
                    {
                        data: 'correlative',
                        name: 's.correlative',
                        serchable: true,
                        orderable: true
                    },
                    {
                        data: 'creator_user_name',
                        name: 's.creator_user_name',
                        serchable: true,
                        orderable: true
                    },
                    {
                        data: 'customer_type_document',
                        name: 's.creator_user_name',
                        serchable: true,
                        orderable: true
                    },
                    {
                        data: 'customer_document_code',
                        name: 's.customer_document_code',
                        serchable: true,
                        orderable: true
                    },
                    {
                        data: 'customer_document_number',
                        name: 's.customer_document_number',
                        serchable: true,
                        orderable: true
                    },
                    {
                        data: 'customer_name',
                        name: 's.customer_name',
                        serchable: true,
                        orderable: true
                    },

                    {
                        data: 'subtotal',
                        name: 'subtotal'
                    },
                    {
                        data: 'igv_amount',
                        name: 'igv_amount'
                    },
                    {
                        data: 'igv_percentage',
                        name: 'igv_percentage'
                    },
                    {
                        data: 'total',
                        name: 'total'
                    },
                    {
                        data: 'sunat_status',
                        name: 'sunat_status'
                    },
                    {
                        data: 'sunat_status',
                        name: 'sunat_status'
                    }
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


        function goToSaleCreate() {
            const route = @json(route('tenant.ventas.comprobante_venta.create'));
            window.location.href = route;
        }

        function filterDataTable() {
            dtContable.ajax.reload();
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

            const url = @json(route('tenant.reportes.contable.excel'));

            const params = {
                date_start: document.querySelector('#date_start').value,
                date_end: document.querySelector('#date_end').value,
                filter_document: document.querySelector('#filter_document').value,
            };

            const queryString = new URLSearchParams(params).toString();

            const finalUrl = `${url}?${queryString}`;
            window.location.href = finalUrl;

        }

        function downloadPdf() {

            const url = @json(route('tenant.reportes.contable.pdf'));

            const params = {
                date_start: document.querySelector('#date_start').value,
                date_end: document.querySelector('#date_end').value,
                filter_document: document.querySelector('#filter_document').value,
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
            dtContable.draw();
        }
    </script>
@endsection
