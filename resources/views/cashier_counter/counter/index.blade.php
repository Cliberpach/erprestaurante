@extends('layouts.template')

@section('title')
    Mostrador Cajero
@endsection

@section('content')
    @include('workshop.quotes.modals.mdl_show_quote')

    <div class="card overflow-hidden">
        <div class="card-header">
            <!-- Fila 1: Título + Botón -->
            <div class="row align-items-center mb-3">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <h6 class="card-title mb-0">Mostrador Cajero</h6>
                </div>
            </div>

            <div class="row">

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
                        <i class="fas fa-circle-check text-primary mr-1"></i> Estado:
                    </label>
                    <select class="form-control" id="status" name="status">
                        <option value="OCUPADO">OCUPADO</option>
                        <option value="FINALIZADO">FINALIZADO</option>
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

        </div>
        <div class="card-body p-0 pb-2">
            @include('cashier_counter.counter.tables.tbl_list')
        </div>
    </div>
@endsection

<style>
    .swal2-container {
        z-index: 9999999;
    }
</style>

@section('js')
    <script>
        let dtList = null;

        document.addEventListener('DOMContentLoaded', () => {
            loadDtList();
            loadTomSelect();
            events();
        })

        function events() {}

        function loadTomSelect() {
            window.clientSelect = new TomSelect('#customer_id', {
                valueField: 'id',
                labelField: 'full_name',
                searchField: ['full_name'],
                plugins: ['clear_button'],
                placeholder: 'Seleccione un cliente',
                maxOptions: 20,
                create: false,
                preload: false,
                // onType: (str) => {
                //     lastCustomerQuery = str;
                // },
                load: async (query, callback) => {
                    if (!query.length) return callback();
                    try {
                        const url = `{{ route('tenant.utils.searchCustomer') }}?q=${encodeURIComponent(query)}`;
                        const response = await fetch(url);
                        if (!response.ok) throw new Error('Error al buscar clientes');
                        const data = await response.json();
                        const results = data.data ?? [];
                        callback(results);
                        // if (results.length === 0) {
                        //     customerParams.documentSearchCustomer = lastCustomerQuery;
                        // }
                    } catch (error) {
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
                    item: (item, escape) => `<div>${escape(item.full_name)}</div>`
                }
            });

        }

        function loadDtList() {
            dtList = new DataTable('#dt-list', {
                "serverSide": true,
                "processing": true,
                responsive: true,
                ajax: {
                    url: '{{ route('tenant.mostrador_cajero.mostrador.getAll') }}',
                    data: function(d) {
                        d.customer_id = $('#customer_id').val();
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.status = $('#status').val();
                    }
                },
                initComplete: function() {
                    $('.dt-search')
                        .append(
                            '<small class="text-muted d-block mt-1" style="text-align:start;">Busca por: Mesa, Usuario, Cliente</small>');
                },
                "columns": [{
                        data: 'order_id',
                        name: 'o.id',
                        "visible": false,
                        "searchable": false
                    },
                    {
                        data: 'table_name',
                        name: 't.name',
                        "searchable": true,
                        "orderable": true
                    },
                    {
                        data: 'created_at',
                        name: 'o.created_at',
                        "searchable": false,
                        "orderable": true,
                        render: function(data) {
                            return formatDateTime(data);
                        }
                    },
                    {
                        data: 'creator_user_name',
                        name: 'o.creator_user_name',
                        searchable: true,
                        orderable: true,
                    },
                    {
                        data: 'customer_name',
                        name: 'o.customer_name',
                        searchable: true,
                        orderable: true,
                    },
                    {
                        data: 'status',
                        name: 'o.status',
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row) {

                            let badgeClass = '';
                            let label = data ?? '';

                            switch (data) {
                                case 'FINALIZADO':
                                    badgeClass = 'badge bg-primary';
                                    break;
                                case 'OCUPADO':
                                    badgeClass = 'badge bg-danger';
                                    break;
                                case 'ANULADO':
                                    badgeClass = 'badge bg-dark';
                                    break;
                                default:
                                    badgeClass = 'badge bg-secondary';
                                    break;
                            }

                            return `<span class="${badgeClass}">${label}</span>`;

                        }
                    },
                    {
                        data: 'total',
                        name: 'o.total',
                        searchable: false,
                        orderable: false,
                        className: "text-end",
                        render: function(data, type, row) {
                            return formatSoles(data);
                        }
                    },
                    {
                        searchable: false,
                        orderable: false,
                        data: null,
                        render: function(data) {
                            let actions = `
                                <div class="dropdown text-center">
                                    <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="fa fa-cog"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">`;

                            if (data.status === 'OCUPADO') {
                                actions += `<li>
                                            <a class="dropdown-item generarPDF"
                                                href="${route('tenant.mostrador_cajero.mostrador.cobrar', data.order_id)}"
                                                title="PDF" role="button" aria-label="Cobrar">
                                                <i class="fas fa-cash-register me-2 text-danger"></i> Cobrar
                                            </a>
                                        </li>`;
                            }
                            actions += `</ul></div>`;
                            return actions;
                        }
                    }
                ],
                language: {
                    decimal: "",
                    emptyTable: "No hay datos disponibles en la tabla",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    infoEmpty: "Mostrando 0 a 0 de 0 registros",
                    infoFiltered: "(filtrado de _MAX_ registros totales)",
                    infoPostFix: "",
                    thousands: ",",
                    lengthMenu: "Mostrar _MENU_ registros",
                    loadingRecords: "Cargando...",
                    processing: "Procesando...",
                    search: "Buscar:",
                    zeroRecords: "No se encontraron registros coincidentes",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior"
                    },
                    aria: {
                        sortAscending: ": activar para ordenar columna ascendente",
                        sortDescending: ": activar para ordenar columna descendente"
                    },
                    select: {
                        rows: {
                            _: "%d filas seleccionadas",
                            0: "Haz clic en una fila para seleccionarla",
                            1: "1 fila seleccionada"
                        }
                    }
                },
                "order": [
                    [0, "desc"]
                ],
            });

        }

        function eliminar(id) {
            const fila = getRowById(dtList, id);
            const htmlVehicleInfo = `
            <div class="card shadow-sm border-0">
                <div class="card-body p-2" style="font-size: 1.2rem;">

                    <div class="mb-1">
                        <i class="fas fa-user text-primary me-1 small"></i>
                        <span class="fw-bold small">Cliente:</span><br>
                        <span class="text-muted small">${fila.customer_name}</span>
                    </div>

                    <div class="mb-1">
                        <i class="fas fa-car text-info me-1 small"></i>
                        <span class="fw-bold small">Placa:</span><br>
                        <span class="text-muted small">${fila.plate}</span>
                    </div>

                    <div class="mb-1">
                        <i class="fas fa-flag text-success me-1 small"></i>
                        <span class="fw-bold small">Marca:</span><br>
                        <span class="text-muted small">${fila.brand_name}</span>
                    </div>

                    <div class="mb-1">
                        <i class="fas fa-tag text-warning me-1 small"></i>
                        <span class="fw-bold small">Modelo:</span><br>
                        <span class="text-muted small">${fila.model_name}</span>
                    </div>

                    <div class="mb-1">
                        <i class="fas fa-calendar-alt text-primary me-1 small"></i>
                        <span class="fw-bold small">Año:</span><br>
                        <span class="text-muted small">${fila.year_name}</span>
                    </div>

                    <div class="mb-0">
                        <i class="fas fa-palette text-danger me-1 small"></i>
                        <span class="fw-bold small">Color:</span><br>
                        <span class="text-muted small">${fila.color_name}</span>
                    </div>

                </div>
            </div>
        `;

            Swal.fire({
                title: '¿Desea eliminar la cotización?',
                html: `${htmlVehicleInfo}`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'No, cancelar',
                focusCancel: true,
                reverseButtons: true
            }).then(async (result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Eliminando vehículo...',
                        html: `
                            <div style="display:flex; align-items:center; justify-content:center; flex-direction:column;">
                                <i class="fa fa-spinner fa-spin fa-3x text-primary mb-3"></i>
                                <p style="margin:0; font-weight:600;">Por favor, espere un momento</p>
                            </div>
                        `,
                        allowOutsideClick: false,
                        showConfirmButton: false
                    });

                    try {
                        const res = await axios.delete(route('tenant.taller.vehiculos.destroy', id));
                        if (res.data.success) {
                            toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                            dtList.ajax.reload();
                        } else {
                            toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                        }
                    } catch (error) {
                        toastr.error(error, 'ERROR EN LA PETICIÓN ELIMINAR COTIZACIÓN');
                    } finally {
                        Swal.close();
                    }

                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: 'Cancelado',
                        text: 'La solicitud ha sido cancelada.',
                        icon: 'error',
                        confirmButtonText: 'Entendido',
                        customClass: {
                            confirmButton: 'btn btn-secondary'
                        },
                        buttonsStyling: false
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

            dtList.ajax.reload();
        }
    </script>
@endsection
