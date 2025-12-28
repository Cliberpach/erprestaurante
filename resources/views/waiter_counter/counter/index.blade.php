@extends('layouts.template')

@section('title')
    Mostrador Mozo
@endsection

@section('content')
    @include('workshop.brands.modals.mdl_create_marca')
    @include('workshop.brands.modals.mdl_edit_marca')
    <div class="card overflow-hidden">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="card-title mb-0">Mostrador Mozo</h6>
            <div class="d-flex flex-wrap gap-2">
                <div class="btn-group btn-group-sm" role="group">
                    <button id="btn-view-circles" class="btn btn-primary active" onclick="setView('circles')">
                        <i class="fas fa-th-large me-1"></i> Mesas
                    </button>

                    <button id="btn-view-table" class="btn btn-outline-primary" onclick="setView('table')">
                        <i class="fas fa-table me-1"></i> Tabla
                    </button>
                </div>

            </div>
        </div>
        <div class="card-body p-0 pb-2">

            <!-- Vista Círculos -->
            <div id="view-circles">
                @include('waiter_counter.counter.grids.grid_list')
            </div>

            <!-- Vista Tabla -->
            <div id="view-table" class="d-none">
                @include('waiter_counter.counter.tables.tbl_list')
            </div>

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
        let dtItems = null;
        let currentView = 'circles';

        document.addEventListener('DOMContentLoaded', () => {
            loadDtItems();
            const savedView = localStorage.getItem('mostrador_view') || 'circles';
            setView(savedView);
            loadTablesAsCircles();
            events();
        })

        function events() {


        }


        function toOrderCreate(tableId) {
            alert(tableId)
            window.location.href = route('tenant.mostrador_mesero.mostrador.create', {
                table: tableId
            });
        }

        function openMdlOrder(tableId) {
            window.location.href = route('tenant.mostrador_mesero.mostrador.create', {
                table: tableId
            });
        }

        async function loadTablesAsCircles() {
            try {
                const response = await axios.get(
                    "{{ route('tenant.mostrador_mesero.mostrador.getAll') }}"
                );

                const data = response.data.data ?? response.data;
                const grid = document.getElementById('tables-grid');
                grid.innerHTML = '';

                data.forEach(item => {

                    let bgClass = 'bg-libre';
                    let statusText = 'LIBRE';

                    if (item.status === 'OCUPADA') {
                        bgClass = 'bg-ocupada';
                        statusText = 'OCUPADA';
                    } else if (!item.status) {
                        bgClass = 'bg-cerrada';
                        statusText = 'LIBRE';
                    }

                    grid.innerHTML += `
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                            <div class="table-card ${bgClass}"
                                data-table="${item.table_id}"
                                data-status="${item.status ?? ''}"
                                style="cursor:pointer">

                                <div class="table-icon">
                                    <i class="fas fa-utensils"></i>
                                </div>

                                <div class="table-number">
                                    ${item.table_name}
                                </div>

                                <div class="table-status">
                                    ${statusText}
                                </div>

                                ${item.total ? `
                                                                <div class="table-total">
                                                                    S/ ${formatSoles(item.total)}
                                                                </div>
                                                                ` : ''}
                            </div>
                        </div>
                    `;

                    if (!grid.dataset.delegateAttached) {
                        grid.addEventListener('click', (e) => {
                            const card = e.target.closest('.table-card');
                            if (!card || !grid.contains(card)) return;

                            const tableId = card.getAttribute('data-table');
                            const status = (card.getAttribute('data-status') || '').toString()
                                .toUpperCase();

                            if (status === 'LIBRE' || !status) {
                                toOrderCreate(tableId);
                            } else {
                                openMdlOrder(tableId);
                            }
                        });

                        grid.dataset.delegateAttached = '1';
                    }
                });

            } catch (error) {
                console.error(error);
                toastr.error('Error al cargar las mesas');
            }
        }

        function loadDtItems() {
            dtItems = new DataTable('#dt-dishes', {
                "serverSide": true,
                "processing": true,
                "ajax": '{{ route('tenant.mostrador_mesero.mostrador.getAll') }}',
                "columns": [{
                        data: 'table_id',
                        name: 't.id',
                        className: "text-center",
                        "visible": false,
                        "searchable": false
                    },
                    {
                        data: 'table_name',
                        name: 't.name',
                        searchable: true,
                        orderable: true,
                    },
                    {
                        data: 'reservation_code',
                        name: 'r.code',
                        searchable: true,
                        orderable: true,
                    },
                    {
                        data: 'creator_user_name',
                        name: 'o.creator_user_name',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: 'customer_name',
                        name: 'o.customer_name',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: 'total',
                        name: 'o.total',
                        searchable: false,
                        orderable: false,
                        render: function(data) {
                            return formatSoles(data);
                        }
                    },
                    {
                        data: 'reservation_date',
                        name: 'o.created_at',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: 'status',
                        name: 'r.status',
                        searchable: false,
                        orderable: false,
                        render: function(data) {
                            let badgeClass = '';
                            let text = data;

                            if (data === 'ACTIVO') {
                                badgeClass = 'bg-primary';
                            } else if (data === 'CERRADO') {
                                badgeClass = 'bg-danger';
                            } else {
                                badgeClass = 'bg-secondary';
                            }

                            return `<span class="badge ${badgeClass}">${text}</span>`;
                        }
                    },
                    {
                        searchable: false,
                        orderable: false,
                        data: null,
                        className: "text-center",
                        render: function(data) {
                            return `
                                <div class="dropdown">
                                    <button
                                        class="btn btn-warning btn-sm dropdown-toggle"
                                        type="button"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a
                                                class="dropdown-item"
                                                href="#"
                                                onclick="redirectParams('tenant.abastecimiento.platos.edit', ${data.id})">
                                                <i class="fa fa-edit text-warning me-2"></i> Editar
                                            </a>
                                        </li>
                                        <li>
                                            <a
                                                class="dropdown-item text-danger"
                                                href="#"
                                                onclick="eliminar(${data.id})">
                                                <i class="fa fa-trash me-2"></i> Eliminar
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            `;
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
                    [0, "asc"]
                ],
            });

        }

        function setView(view) {
            localStorage.setItem('mostrador_view', view);

            const circles = document.getElementById('view-circles');
            const table = document.getElementById('view-table');

            const btnCircles = document.getElementById('btn-view-circles');
            const btnTable = document.getElementById('btn-view-table');

            if (view === 'circles') {
                circles.classList.remove('d-none');
                table.classList.add('d-none');

                btnCircles.classList.add('btn-primary', 'active');
                btnCircles.classList.remove('btn-outline-primary');

                btnTable.classList.add('btn-outline-primary');
                btnTable.classList.remove('btn-primary', 'active');

                loadTablesAsCircles(); // 🔁 refresca
            } else {
                circles.classList.add('d-none');
                table.classList.remove('d-none');

                btnTable.classList.add('btn-primary', 'active');
                btnTable.classList.remove('btn-outline-primary');

                btnCircles.classList.add('btn-outline-primary');
                btnCircles.classList.remove('btn-primary', 'active');

                if (dtItems) {
                    dtItems.ajax.reload(null, false);
                }
            }
        }

        function eliminar(id) {
            const fila = getRowById(dtItems, id);
            const htmlInfo = `
                <div class="card shadow-sm border-0">
                    <div class="card-body p-2" style="font-size: 1.2rem;">

                        <div class="mb-1">
                            <i class="fas fa-user text-primary me-1 small"></i>
                            <span class="fw-bold small">Nombre:</span><br>
                            <span class="text-muted small">${fila.name}</span>
                        </div>

                        <div class="mb-1">
                            <i class="fas fa-utensils text-info me-1 small"></i>
                            <span class="fw-bold small">Tipo:</span><br>
                            <span class="text-muted small">${fila.type_dish_name}</span>
                        </div>

                        <div class="mb-1">
                            <i class="fas fa-flag text-success me-1 small"></i>
                            <span class="fw-bold small">P.Costo:</span><br>
                            <span class="text-muted small">${formatSoles(fila.purchase_price)}</span>
                        </div>

                        <div class="mb-1">
                            <i class="fas fa-tag text-warning me-1 small"></i>
                            <span class="fw-bold small">P.Venta:</span><br>
                            <span class="text-muted small">${formatSoles(fila.sale_price)}</span>
                        </div>
                    </div>
                </div>
            `;

            Swal.fire({
                title: '¿Desea eliminar el plato?',
                html: `${htmlInfo}`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'No, cancelar',
                focusCancel: true,
                reverseButtons: true
            }).then(async (result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Eliminando plato...',
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
                        const res = await axios.delete(route('tenant.abastecimiento.platos.destroy', id));
                        if (res.data.success) {
                            toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                            dtItems.ajax.reload();
                        } else {
                            toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                        }
                    } catch (error) {
                        toastr.error(error, 'ERROR EN LA PETICIÓN ELIMINAR PLATO');
                    } finally {
                        Swal.close();
                    }

                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    swalWithBootstrapButtons.fire({
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
    </script>
@endsection
