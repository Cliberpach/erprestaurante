@extends('layouts.template')

@section('title')
    LISTA DE ROLES
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h4 class="card-title mb-md-0 mb-2">Roles</h4>

            <div class="d-flex flex-wrap gap-2">
                <a onclick="goToCrearRol()" class="btn btn-primary text-white">
                    <i class="fas fa-plus-circle"></i> Nuevo
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <div class="table-responsive">
                        @include('maintenance.roles.tables.table_list_roles')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        let dtRoles = null;

        document.addEventListener('DOMContentLoaded', () => {
            loadDtRoles();
        })

        function loadDtRoles() {
            const urlGetRoles = '{{ route('tenant.mantenimiento.roles.getAll') }}';

            dtRoles = new DataTable('#table_roles', {
                serverSide: true,
                processing: true,
                responsive: true,
                ajax: {
                    url: urlGetRoles,
                    type: 'GET',
                },
                order: [
                    [0, 'desc']
                ],
                columns: [{
                        data: 'id',
                        name: 'r.id',
                        searchable: false,
                        orderable: true
                    },
                    {
                        data: 'name',
                        name: 'r.name',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'created_at',
                        name: 'r.created_at',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            const baseUrlEdit =
                                `{{ route('tenant.mantenimiento.roles.edit', ['id' => ':id']) }}`;
                            urlEdit = baseUrlEdit.replace(':id', data.id);

                            const urlDelete = `{{ route('tenant.mantenimiento.roles.destroy', ':id') }}`
                                .replace(':id', data.id);

                            return `
                            <div class="btn-group">
                            <button type="button" class="dropdown-toggle btn btn-primary" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-grip"></i>
                            </button>
                            <ul class="dropdown-menu" style="max-height: 100px; overflow-y: auto;">
                                <li>
                                    <a class="dropdown-item" href="${urlEdit}">
                                        <i class="fa-solid fa-pen-to-square"></i> Editar
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="eliminarRol(${data.id})">
                                        <i class="fa-solid fa-trash"></i> Eliminar
                                    </a>
                                </li>
                            </ul>
                            </div>
                        `;
                        },
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
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

        function goToCrearRol() {
            window.location.href = @json(route('tenant.mantenimiento.roles.create'));
        }



        function eliminarRol(id) {
            toastr.clear();
            let row = getRowById(dtRoles, id);
            let message = '';
            let tipo_documento = '';

            message = `Desea eliminar el rol: ${row.nombre}`;

            Swal.fire({
                title: message,
                text: "Operación no reversible!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar!",
                cancelButtonText: "No, cancelar!",
                reverseButtons: true
            }).then(async (result) => {
                if (result.isConfirmed) {

                    Swal.fire({
                        title: 'Cargando...',
                        html: 'Eliminando rol...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    try {
                        let urlDeleteRol = `{{ route('tenant.mantenimiento.roles.destroy', ['id' => ':id']) }}`;
                        urlDeleteRol = urlDeleteRol.replace(':id', id);
                        const token = document.querySelector('input[name="_token"]').value;

                        const response = await fetch(urlDeleteRol, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': token
                            }
                        });

                        const res = await response.json();

                        if (res.success) {
                            dtRoles.draw();
                            toastr.success(res.message, 'OPERACIÓN COMPLETADA');
                        } else {
                            toastr.error(res.message, 'ERROR EN EL SERVIDOR AL ELIMINAR ROL');
                        }

                    } catch (error) {
                        toastr.error(error, 'ERROR EN LA PETICIÓN ELIMINAR ROL');
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
