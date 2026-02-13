@extends('layouts.template')

@section('title')
    Métodos pago
@endsection

@section('content')
    @include('sales.payment_method.modals.mdl_create_payment_method')
    @include('sales.payment_method.modals.mdl_edit_payment_method')


    <div class="card">
        @csrf
        <div class="card-header d-flex justify-content-between flex-row">
            <h4 class="card-title">Métodos pago <i class="fas fa-wallet" style="color: rgb(7, 45, 168);"></i></h4>
            <div class="input-group-append">
                <button onclick="openMdlCreatePaymentMethod()" type="button" data-bs-whatever="Nueva caja"
                    class="btn btn-primary btn-add-new" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    <div class="lign-items-center d-flex align-items-center">
                        <i class="fas fa-plus pe-1"></i>
                        <p class="mb-0 ml-2"> NUEVO</p>
                    </div>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        @include('sales.payment_method.tables.tbl_list_payment_methods')
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('js')
    <script>
        let dtPaymentMethods = null;

        document.addEventListener('DOMContentLoaded', () => {
            loadDtPaymentMethods();
            iniciarSelect2();
            events();
        })

        function events() {
            eventsMdlCreatePaymentMethod();
            eventsMdlEditPaymentMethod();
        }

        function iniciarSelect2() {
            $('.select2_form').select2({
                theme: "bootstrap-5",
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                placeholder: $(this).data('placeholder'),
                allowClear: true,
            });
        }

        function loadDtPaymentMethods() {
            const urlGet = `{{ route('tenant.ventas.metodos_pago.getPaymentMethods') }}`;

            dtPaymentMethods = new DataTable('#tbl_list_payment_methods', {
                serverSide: true,
                processing: true,
                responsive: true,
                ajax: {
                    url: urlGet,
                    type: 'GET',
                },
                order: [
                    [0, 'desc']
                ],
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            const routeAccounts = route('tenant.ventas.metodos_pago.assignAccountsCreate', {
                                id: row.id
                            });

                            let options = `  <div class="btn-group dropdown">
                            <button type="button" class="dropdown-toggle btn btn-primary btn-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-grip"></i>
                            </button>
                            <ul class="dropdown-menu" style="max-height: 150px; overflow-y: auto;">`;

                            if (data.id != 1) {
                                options += `<li>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="openMdlEditPaymentMethod(${data.id})">
                                        <i class="fa-solid fa-pen-to-square"></i> Editar
                                    </a>
                                </li>`;
                            }

                            options += `<li>
                                    <a class="dropdown-item" href="${routeAccounts}" >
                                        <i class="fas fa-piggy-bank"></i> Cuentas
                                    </a>
                                </li></ul>
                                </div>`;


                            return options;

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

        function destroyPaymentMethod(id) {
            toastr.clear();
            let row = getRowById(dtPaymentMethods, id);
            let message = '';
            let tipo_documento = '';

            Swal.fire({
                title: `
                                            Desea eliminar el método de pago ? `,
                text: `
                                            Almacén: $ {
                                                row.nombre
                                            }
                                            `,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar!",
                cancelButtonText: "No, cancelar!",
                reverseButtons: true
            }).then(async (result) => {
                if (result.isConfirmed) {

                    Swal.fire({
                        title: 'Cargando...',
                        html: 'Eliminando método de pago...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    try {
                        let url = null;
                        url = url.replace(':id', id);
                        const token = document.querySelector('input[name="_token"]').value;

                        const response = await fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': token
                            }
                        });

                        const res = await response.json();

                        if (res.success) {
                            dtPaymentMethods.draw();
                            toastr.success(res.message, 'OPERACIÓN COMPLETADA');
                        } else {
                            toastr.error(res.message, 'ERROR EN EL SERVIDOR AL ELIMINAR MÉTODO DE PAGO');
                        }

                    } catch (error) {
                        toastr.error(error, 'ERROR EN LA PETICIÓN ELIMINAR MÉTODO DE PAGO');
                    } finally {
                        Swal.close();
                    }

                } else if (
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
