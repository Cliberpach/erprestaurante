@extends('layouts.template')

@section('title')
    Movimientos de Caja
@endsection

@section('css')
    <style>
        .my-swal {
            z-index: 3000 !important;
        }
    </style>
@endsection

@section('content')
    @include('cash.petty-cash-book.modals.mdl_open_cash')
    @include('cash.petty-cash-book.modals.mdl_close_cash')
    @include('cash.petty-cash-book.modals.mdl_edit_book')
    <div class="card overflow-hidden">
        <div class="card-header">
            <div class="row mb-2 justify-content-between align-items-center">
                <div class="col-6">
                    <h6 class="card-title mb-0">MOVIMIENTOS DE CAJA</h6>
                </div>
                <div class="col-6 text-end">
                    <a onclick="openMdlOpenCash()" class="btn btn-primary text-white">
                        <i class="fas fa-plus-circle"></i> NUEVO
                    </a>
                    <button type="button" class="btn btn-warning" onclick="filterData()">
                        <i class="fas fa-filter me-1"></i> Filtrar
                    </button>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-2">
                    <label class="form-label fw-bold">
                        <i class="fas fa-calendar-alt text-success me-1"></i> Fecha inicio:
                    </label>
                    <input type="date" class="form-control" id="start_date" name="start_date">
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-2">
                    <label class="form-label fw-bold">
                        <i class="fas fa-calendar-check text-danger me-1"></i> Fecha fin:
                    </label>
                    <input type="date" class="form-control" id="end_date" name="end_date">
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6 col-12 mb-2">
                    <label class="form-label fw-bold">
                        <i class="fas fa-circle-check text-primary me-1"></i> Estado:
                    </label>
                    <select class="form-control" id="filter_status" name="filter_status">
                        <option value="">TODOS</option>
                        <option value="ABIERTO">ABIERTO</option>
                        <option value="CERRADO">CERRADO</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body p-0 pb-2">
            @include('cash.petty-cash-book.tables.tbl_list')
        </div>
    </div>
@endsection

@section('js')
    <script>
        let dtCash = null;

        document.addEventListener('DOMContentLoaded', () => {
            iniciarDtCash();
            events();
        })

        function events() {
            eventsMdlOpenCash();
            eventsMdlCloseCash();
            eventsMdlEditBook();
        }

        function iniciarDtCash() {
            dtCash = new DataTable('#dt-cash-books', {
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: '{{ route('tenant.cajas.apertura_cierre.getCashBooks') }}',
                    type: 'GET',
                    data: function(d) {
                        d.start_date    = $('#start_date').val();
                        d.end_date      = $('#end_date').val();
                        d.filter_status = $('#filter_status').val();
                    }
                },
                "columns": [{
                        data: 'id',
                        className: "text-center",
                        "visible": false,
                        "searchable": false
                    },
                    {
                        data: 'code',
                        name: 'code',
                        searchable: true,
                        orderable: true,
                        className: "text-center"
                    },
                    {
                        data: 'user_name',
                        name: 'u.name',
                        searchable: true,
                        orderable: true,
                        className: "text-center"
                    },
                    {
                        data: 'petty_cash_name',
                        name: 'c.petty_cash_name',
                        searchable: true,
                        orderable: true,
                        className: "text-center"
                    },
                    {
                        data: 'initial_amount',
                        name: 'c.initial_amount',
                        searchable: false,
                        orderable: false,
                        className: "text-center"
                    },
                    {
                        data: 'initial_date',
                        name: 'c.initial_date',
                        searchable: true,
                        orderable: true,
                        className: "text-center"
                    },
                    {
                        data: 'final_date',
                        name: 'c.final_date',
                        searchable: true,
                        orderable: true,
                        className: "text-center"
                    },
                    {
                        data: 'closing_amount',
                        name: 'c.closing_amount',
                        searchable: false,
                        orderable: false,
                        className: "text-center"
                    },
                    {
                        data: 'status',
                        name: 'c.status',
                        searchable: true,
                        orderable: false,
                        className: "text-center",
                        render: function(status) {

                            let badgeClass = '';

                            switch (status) {

                                case 'CERRADO':
                                    badgeClass = 'bg-danger';
                                    break;

                                case 'ABIERTO':
                                    badgeClass = 'bg-primary';
                                    break;
                            }

                            return `<span class="badge ${badgeClass}">${status}</span>`;
                        }
                    },
                    {
                        searchable: false,
                        orderable: false,
                        data: null,
                        className: "text-center",
                        render: function(data, type, row) {

                            const pdfUrl = route('tenant.cajas.apertura_cierre.pdf', {
                                id: data.id
                            });

                            let actions = `<div class="dropdown">
                                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-cog"></i>
                                            </button>

                                            <ul class="dropdown-menu dropdown-menu-end">`;

                            if (data.status === 'ABIERTO') {
                                actions += `
                                    <li>
                                        <button class="dropdown-item text-primary fw-semibold"
                                                onclick="openMdlCloseCash(${data.id})">
                                            <i class="fas fa-lock me-1"></i> Cerrar caja
                                        </button>
                                    </li>
                                    <li>
                                        <button class="dropdown-item text-primary fw-semibold"
                                                onclick="openMdlEditBook(${data.id})">
                                            <i class="fas fa-edit me-1"></i> Editar
                                        </button>
                                    </li>
                                `;
                            }

                            actions += `<li>
                                            <a class="dropdown-item text-danger fw-semibold" target="_blank" href="${pdfUrl}">
                                                <i class="far fa-file-pdf me-1"></i> PDF
                                            </a>
                                        </li>`;

                            if (data.status === 'ABIERTO' && !data.programming_id) {
                                actions += `<li>
                                            <a class="dropdown-item fw-semibold text-primary" href="javascript:void(0);" onclick="programmingAuto(${data.id})">
                                                <i class="fas fa-utensils text-primary me-1"></i> Programación
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

        function filterData() {
            const startDate = document.getElementById('start_date')?.value;
            const endDate   = document.getElementById('end_date')?.value;

            if (startDate && endDate && startDate > endDate) {
                toastr.error('La fecha inicio no puede ser mayor que la fecha fin', 'Fechas inválidas');
                return;
            }

            dtCash.ajax.reload();
        }

        function programmingAuto(id) {
            toastr.clear();
            let row = getRowById(dtCash, id);

            let message = `
                <div class="text-center">
                    <p class="mb-2">
                        <i class="fas fa-receipt text-primary me-2"></i>
                        <strong>Código:</strong> ${row.code}
                    </p>
                    <p class="mb-2">
                        <i class="fas fa-cash-register text-success me-2"></i>
                        <strong>Caja:</strong> ${row.petty_cash_name}
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-user text-secondary me-2"></i>
                        <strong>Cajero:</strong> ${row.user_name}
                    </p>
                </div>
            `;

            Swal.fire({
                title: 'Generar programación auto?',
                html: message,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí!",
                cancelButtonText: "No!",
                reverseButtons: true
            }).then(async (result) => {
                if (result.isConfirmed) {

                    Swal.fire({
                        title: 'Cargando...',
                        html: 'Generando programación...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    try {

                        const formData = new FormData();
                        formData.append('id', id);

                        const res = await axios.post(route('tenant.cajas.apertura_cierre.programming'),
                            formData);

                        if (res.data.success) {
                            dtCash.ajax.reload();
                            toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                        } else {
                            toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                        }

                    } catch (error) {
                        toastr.error(error, 'ERROR EN LA PETICIÓN GENERAR PROGRAMACIÓN AUTO');
                    } finally {
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
    </script>
@endsection
