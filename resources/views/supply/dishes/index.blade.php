@extends('layouts.template')

@section('title')
    Platos
@endsection

@section('content')
    @include('workshop.brands.modals.mdl_create_marca')
    @include('workshop.brands.modals.mdl_edit_marca')
    <div class="card overflow-hidden">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="card-title mb-0">LISTA DE PLATOS</h6>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('tenant.abastecimiento.platos.create') }}" class="btn btn-primary text-white">
                    <i class="fas fa-plus-circle"></i> Nuevo
                </a>
            </div>
        </div>
        <div class="card-body p-0 pb-2">
            @include('supply.dishes.tables.tbl_list')
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

        document.addEventListener('DOMContentLoaded', () => {
            loadDtItems();
            events();
        })

        function events() {}

        function loadDtItems() {
            dtItems = new DataTable('#dt-dishes', {
                "serverSide": true,
                "processing": true,
                "ajax": '{{ route('tenant.abastecimiento.platos.getList') }}',
                "columns": [{
                        data: 'id',
                        name: 'd.id',
                        className: "text-center",
                        "visible": false,
                        "searchable": false
                    },
                    {
                        data: 'name',
                        name: 'd.name',
                        searchable: true,
                        orderable: true,
                        className: "text-center"
                    },
                    {
                        data: 'type_dish_name',
                        name: 'td.name',
                        searchable: true,
                        orderable: true,
                        className: "text-center"
                    },
                    {
                        data: 'purchase_price',
                        name: 'd.purchase_price',
                        searchable: false,
                        orderable: true,
                        className: "text-center",
                        render: function(data, type, row) {
                            return formatSoles(data);
                        }
                    },
                    {
                        data: 'sale_price',
                        name: 'dm.sale_price',
                        searchable: false,
                        orderable: true,
                        className: "text-center",
                        render: function(data, type, row) {
                            return formatSoles(data);
                        }
                    },
                    {
                        data: 'img_route',
                        name: 'd.img_route',
                        searchable: true,
                        orderable: true,
                        className: "text-center",
                        render: function(data, type, row) {

                            const ASSET_URL = @json(asset(''));

                            if (!data) {
                                return `<span class="text-muted">Sin imagen</span>`;
                            }

                            return `
                                <img
                                    src="${ASSET_URL}${data}"
                                    alt="img"
                                    style="
                                        width: 45px;
                                        height: 45px;
                                        object-fit: cover;
                                        border-radius: 6px;
                                    "
                                >
                            `;
                        }
                    },
                    {
                        data: 'creator_user_name',
                        name: 'd.creator_user_name',
                        searchable: true,
                        orderable: false,
                        className: "text-center"
                    },
                    {
                        searchable: false,
                        orderable: false,
                        data: null,
                        className: "text-center",
                        render: function(data) {

                            return `
                            <div class="btn-group">
                                <button
                                    class="btn btn-warning btn-sm modificarDetalle"
                                    onclick="redirectParams('tenant.abastecimiento.platos.edit',${data.id})"
                                    type="button"
                                    title="Modificar">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <a
                                    class="btn btn-danger btn-sm"
                                    href="#"
                                    onclick="eliminar(${data.id})"
                                    title="Eliminar">
                                    <i class="fa fa-trash"></i>
                                </a>
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
                    [0, "desc"]
                ],
            });

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
