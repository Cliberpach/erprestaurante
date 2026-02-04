@extends('layouts.template')

@section('title')
    Ventas
@endsection

@section('content')
    @include('sales.sale_document.modals.mdl_nota_credito')
    @include('sales.sale_document.modals.mdl_convert')
    <div class="card overflow-hidden">
        <div class="card-header">

            <div class="row mb-2 justify-between">
                <div class="col-6">
                    <h6 class="card-title mb-0">LISTA DE VENTAS</h6>
                </div>
                <div class="col-6" style="text-align: end;">
                    <div class="input-group-append">
                        <button onclick="goToSaleCreate()" type="button" data-bs-whatever="Nueva caja"
                            class="btn btn-primary btn-add-new" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            <div class="lign-items-center d-flex align-items-center">
                                <i class="fas fa-plus pe-1"></i>
                                <p class="mb-0 ml-2"> NUEVO</p>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <div class="row mb-2">

                <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 mb-2">
                    <label class="form-label fw-bold">
                        <i class="fas fa-user text-primary mr-1"></i> Cliente:
                    </label>
                    <select class="form-control" id="customer_id" name="customer_id">
                        <option value="">Seleccione un cliente</option>
                    </select>
                    <p class="customer_id_error msgError mb-0"></p>
                </div>

                <div class="col-lg-2 col-md-6 col-sm-12 col-xs-12 mb-2">
                    <label class="form-label fw-bold">
                        <i class="fas fa-circle-check text-primary mr-1"></i> Sunat:
                    </label>
                    <select class="form-control" id="status" name="status">
                        <option value="PENDIENTE">PENDIENTE</option>
                        <option value="ENVIADO">ENVIADO</option>
                        <option value="ACEPTADO">ACEPTADO</option>
                        <option value="RECHAZADO">RECHAZADO</option>
                        <option value="ANULADO">ANULADO</option>
                        <option value="ANULADO PARCIAL">ANULADO PARCIAL</option>
                    </select>
                    <p class="status_error msgError mb-0"></p>
                </div>

                <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12 mb-2">
                    <label class="form-label fw-bold">
                        <i class="fas fa-calendar-alt text-success mr-1"></i> Fecha inicio:
                    </label>
                    <input type="date" class="form-control" id="start_date" name="start_date">
                </div>

                <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12 mb-2">
                    <label class="form-label fw-bold">
                        <i class="fas fa-calendar-check text-danger mr-1"></i> Fecha fin:
                    </label>
                    <input type="date" class="form-control" id="end_date" name="end_date">
                </div>

                <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 mb-2 text-end">
                    <button type="button" id="btn-filter" class="btn btn-primary btn-block" onclick="filterData();">
                        <i class="fas fa-filter mr-1"></i> Filtrar
                    </button>
                </div>

            </div>

            <div class="row align-items-center text-muted mb-2" style="font-size: 0.85rem;">
                <div class="d-flex align-items-center col-auto">
                    <span
                        style="
                        width: 12px;
                        height: 12px;
                        background-color: #6f42c1;
                        border-radius: 3px;
                        display: inline-block;
                        margin-right: 6px;
                        opacity: .6;
                    ">
                    </span>
                    <i class="fas fa-right-left mx-1" style="color:#6f42c1;"></i>
                    <span>Venta convertida</span>
                </div>
            </div>
        </div>

        <div class="card-body p-0 pb-2">
            @include('sales.sale_document.tables.tbl_list_sales')
        </div>
    </div>
@endsection

<style>
    /* Ventas convertidas */
    .row-sale-converted {
        background: linear-gradient(90deg,
                rgba(111, 66, 193, 0.35),
                rgba(111, 66, 193, 0.10)) !important;

        border-left: 8px solid #6f42c1;
        box-shadow: inset 6px 0 0 rgba(111, 66, 193, 0.15);

        transition:
            background 0.25s ease,
            box-shadow 0.25s ease,
            transform 0.15s ease;
    }

    /* Hover con presencia */
    .row-sale-converted:hover {
        background: linear-gradient(90deg,
                rgba(111, 66, 193, 0.45),
                rgba(111, 66, 193, 0.18)) !important;

        box-shadow:
            inset 6px 0 0 rgba(111, 66, 193, 0.25),
            0 2px 10px rgba(111, 66, 193, 0.15);
    }

    /* Texto ligeramente más fuerte */
    .row-sale-converted td {
        vertical-align: middle;
        font-weight: 500;
    }
</style>

@section('js')
    <script>
        let dtSales = null;

        document.addEventListener('DOMContentLoaded', () => {
            loadSelectIndex();
            events();
        })

        function events() {
            startDataTableSales();
            eventsMdlConvert();
            eventsMdlNc();
        }

        function startDataTableSales() {
            const urlGetSales = '{{ route('tenant.ventas.comprobante_venta.getSales') }}';

            dtSales = new DataTable('#tbl_list_sales', {
                responsive: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: urlGetSales,
                    type: 'GET',
                    data: function(d) {
                        d.customer_id = $('#customer_id').val();
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.status = $('#status').val();
                    }
                },
                order: [
                    [0, 'desc']
                ],
                rowCallback: function(row, data) {
                    if (data.converted_to_id) {
                        $(row).addClass('row-sale-converted');
                    }
                },
                columns: [

                    {
                        data: 'id',
                        name: 'id',
                        searchable: false,
                        orderable: true
                    },
                    {
                        data: 'fecha_registro',
                        name: 'fecha_registro',
                        searchable: false,
                        orderable: true
                    },
                    {
                        data: 'customer_full_name',
                        name: 'customer_full_name',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'doc',
                        name: 'doc',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'total',
                        name: 'total',
                        searchable: false,
                        orderable: true
                    },
                    {
                        data: null,
                        render: function(data, type, row) {

                            let badge_class = '';

                            if (data.sunat_status === 'PENDIENTE') {
                                badge_class = 'danger';
                            }
                            if (data.sunat_status === 'ENVIADO') {
                                badge_class = 'warning';
                            }
                            if (data.sunat_status === 'ACEPTADO') {
                                badge_class = 'primary';
                            }
                            if (data.sunat_status === 'RECHAZADO') {
                                badge_class = 'dark';
                            }

                            return `<span class="badge bg-${badge_class}">${data.sunat_status}</span>`;
                        },
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: null,
                        render: function(data, type, row) {

                            const urlDownloadXml =
                                "{{ route('tenant.ventas.comprobante_venta.downloadXml', ':id') }}".replace(
                                    ':id', data.id);
                            const urlDownloadCdr =
                                "{{ route('tenant.ventas.comprobante_venta.downloadCdr', ':id') }}".replace(
                                    ':id', data.id);
                            const urlPdf =
                                "{{ route('tenant.ventas.comprobante_venta.pdf_voucher', ':id') }}".replace(
                                    ':id', data.id);

                            let descargas =
                                `<div style="display: flex; justify-content: flex-start; gap: 10px; flex-wrap: nowrap;">`;

                            descargas += `<a target="_blank" class="btn btn-danger" style="color:white; max-width: 150px; flex-shrink: 0;" href="${urlPdf}">
                                            <i class="fa-solid fa-file-pdf"></i> PDF
                                        </a>`;

                            if (data.ruta_xml) {
                                const asset_route = @json(asset(''));
                                descargas += `<a class="btn btn-success" style="color:white; max-width: 150px; flex-shrink: 0;" href="${urlDownloadXml}" >
                                                <i class="fa-solid fa-file-excel"></i> XML
                                            </a>`;
                            }

                            if (data.ruta_cdr) {
                                const asset_route = @json(asset(''));
                                descargas += `<a class="btn btn-primary" style="color:white; max-width: 150px; flex-shrink: 0;" href="${urlDownloadCdr}">
                                                <i class="fa-solid fa-book"></i> CDR
                                            </a>`;
                            }

                            descargas += `</div>`;

                            return descargas;
                        },
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: null,
                        render: function(data, type, row) {

                            let acciones = `<div class="btn-group float-end">
                                                <button
                                                    class="btn btn-white btn-sm btn-shadow btn-icon waves-effect dropdown-toggle"
                                                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fi fi-rr-menu-dots"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">`;

                            if (data.type_sale_code === '03' || data.type_sale_code === '01') {
                                if (data.sunat_status === 'PENDIENTE' || data.sunat_status ===
                                    'RECHAZADO') {
                                    acciones += `<li>
                                                    <a class="dropdown-item text-primary" href="javascript:void(0);" onclick="sendSunat(${data.id})">
                                                        <i class="fa-solid fa-paper-plane"></i> Sunat
                                                    </a>
                                                </li>`;
                                }
                            }

                            if (!data.converted_from_id && !data.converted_to_id && data.type_sale_code ===
                                'NV') {
                                acciones += `<li>
                                    <a  href="javascript:void(0);" class="dropdown-item text-primary" onclick="openMdlConvert(${data.id})">
                                        <i class="fas fa-right-left"></i> Convertir
                                    </a>
                                </li>`;
                            }

                            if (data.sunat_status === 'ACEPTADO') {
                                acciones += `<li>
                                                    <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="openMdlNotaCredito(${data.id})">
                                                        <i class="fa-solid fa-ban"></i> Anular
                                                    </a>
                                                </li>`;
                            }

                            acciones += `</ul></div>`;

                            return acciones;
                        },
                        name: 'actions',
                        orderable: false,
                        searchable: false
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

        function loadSelectIndex() {
            window.customerSelect = new TomSelect('#customer_id', {
                valueField: 'id',
                labelField: 'full_name',
                searchField: ['full_name'],
                plugins: ['clear_button'],
                placeholder: 'Seleccione un cliente',
                maxOptions: 20,
                create: false,
                preload: false,
                load: async (query, callback) => {
                    if (query.length < 3) return callback();
                    try {
                        const url =
                            `{{ route('tenant.utils.searchCustomer') }}?q=${encodeURIComponent(query)}`;
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
                            <strong>${escape(item.full_name)}</strong><br>
                            <small>${escape(item.email ?? '')}</small>
                        </div>
                    `,
                    item: (item, escape) => `<div>${escape(item.full_name)}</div>`,
                    no_results: function(data, escape) {
                        return `
                            <div class="no-results">
                                <i class="fas fa-search" style="margin-right:6px; color:#17a2b8;"></i>
                                Sin resultados
                            </div>
                        `;
                    }
                }
            });
        }

        function goToSaleCreate() {
            const route = @json(route('tenant.ventas.comprobante_venta.create'));
            window.location.href = route;
        }

        async function sendSunat(saleId) {

            const sale_document = getRowById(dtSales, saleId);

            let message = `Enviar el documento: ${sale_document.serie}-${sale_document.correlative} a Sunat?`;

            Swal.fire({
                title: message,
                text: "OPERACIÓN NO REVERSIBLE!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, enviar!",
                cancelButtonText: "No, cancelar!",
                reverseButtons: true
            }).then(async (result) => {
                if (result.isConfirmed) {

                    Swal.fire({
                        title: `Documento de venta: ${sale_document.serie}-${sale_document.correlative}`,
                        html: "ENVIANDO A SUNAT...",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    try {
                        toastr.clear();

                        const formData = new FormData();
                        const urlInvoice = @json(route('tenant.ventas.comprobante_venta.send_sunat'));

                        formData.append('sale_id', saleId);

                        const res = await axios.post(urlInvoice, formData);

                        Swal.close();

                        if (res.data.success) {
                            dtSales.ajax.reload(null, false);
                            toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                            Swal.close();
                        } else {
                            toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                            Swal.close();
                        }

                    } catch (error) {
                        toastr.error(error, 'ERROR EN LA PETICIÓN ENVIAR A SUNAT');
                        Swal.close();
                    }

                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    Swal.fire({
                        title: "Operación cancelada",
                        text: "No se realizaron acciones",
                        icon: "error"
                    });
                }
            });
        }

        function filterData() {
            const startDate = document.getElementById('start_date')?.value;
            const endDate = document.getElementById('end_date')?.value;

            if (startDate && endDate) {
                if (startDate > endDate) {
                    toastr.error(
                        'La fecha inicio no puede ser mayor que la fecha fin',
                        'Fechas inválidas'
                    );
                    return;
                }
            }

            dtSales.ajax.reload();
        }
    </script>
@endsection
