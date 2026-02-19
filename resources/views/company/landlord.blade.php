@extends('layouts.template')

@section('title')
    Empresa
@endsection

@section('css')
@endsection

@section('content')
    <div class="card">
        @csrf
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">MANTENIMIENTO DE EMPRESAS</h5>
            <span class="float-end">
                <button type="button" onclick="window.location.href='{{ route('landlord.mantenimiento.empresas.create') }}'"
                    class="btn btn-primary me-1">
                    <i class="fas fa-plus"></i> NUEVO
                </button> </span>
        </div>
        <div class="table-responsive text-nowrap">

            @include('company.tables.tbl_landlord_companies')

        </div>
    </div>
@endsection


@section('js')
    <script>
        let dtCompaniesLandlord = null;

        document.addEventListener('DOMContentLoaded', () => {
            events();
            startDataTableCompanies();
        })

        function events() {
            document.addEventListener('click', (e) => {
                const targetChkBlock = e.target.closest('.chk-block_account');
                if (targetChkBlock) {
                    actionChangeBlockAccount(targetChkBlock);
                }
            })
        }

        function startDataTableCompanies() {
            const urlGetCompanies = '{{ route('landlord.mantenimiento.empresas.getCompanies') }}';

            dtCompaniesLandlord = new DataTable('#tbl_landlord_companies', {
                serverSide: true,
                processing: true,
                responsive: true,
                ajax: {
                    url: urlGetCompanies,
                    type: 'GET'
                },
                order: [
                    [0, 'desc']
                ],
                columns: [{
                        data: 'domain',
                        render: function(data, type, row) {
                            return `<a href="https://${data}/login" target="_blank">${data}</a>`;
                        },
                        name: 'domain',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'business_name',
                        name: 'business_name'
                    },
                    {
                        data: 'ruc',
                        name: 'ruc'
                    },
                    {
                        data: 'plan_name',
                        name: 'plan_name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'block_account',
                        name: 'e.block_account',
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row, meta) {
                            const checked = data == 1 ? 'checked' : '';

                            let option = `<div class="form-check form-switch text-center">
                                <input
                                    ${checked}
                                    class="form-check-input chk-block_account"
                                    type="checkbox"
                                    value="${data}"
                                    data-company="${row.id}"
                                >
                            </div>`;
                            return option;
                        },
                    },
                    {
                        data: 'invoicing_status',
                        render: function(data) {
                            return data === 1 ? 'SI' : 'NO';
                        },
                        name: 'invoicing_status'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: null,
                        render: function(data) {

                            const urlEditCompany =
                                "{{ route('landlord.mantenimiento.empresas.edit', ':id') }}".replace(':id',
                                    data.id);

                            return `<div class="btn-group">
                            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bars"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="resetPassword(${data.id});">
                                        <i class="fas fa-key"></i> Resetear clave
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="${urlEditCompany}">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="deleteTenant(${data.id});">
                                        <i class="fas fa-trash-alt"></i> Eliminar
                                    </a>
                                </li>
                            </ul>
                            </div>`;
                        },
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                pageLength: 25,
                lengthChange: false,
                language: {
                    lengthMenu: "Mostrar _MENU_ registros por página",
                    zeroRecords: "No se encontraron resultados",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    infoEmpty: "Mostrando 0 a 0 de 0 registros",
                    infoFiltered: "(filtrado de _MAX_ registros totales)",
                    search: "Buscar:",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior"
                    },
                    loadingRecords: "Cargando...",
                    processing: "Procesando...",
                    emptyTable: "No hay datos disponibles en la tabla",
                    aria: {
                        sortAscending: ": activar para ordenar la columna de manera ascendente",
                        sortDescending: ": activar para ordenar la columna de manera descendente"
                    }
                }
            });
        }

        function resetPassword(company_id) {

            const company = getRowById(dtCompaniesLandlord, company_id);

            let message = `Resetear la clave de: ${company.business_name}-${company.ruc}?`;

            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: "btn btn-success",
                    cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });
            swalWithBootstrapButtons.fire({
                title: message,
                text: "OPERACIÓN NO REVERSIBLE!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, resetear!",
                cancelButtonText: "No, cancelar!",
                reverseButtons: true
            }).then(async (result) => {
                if (result.isConfirmed) {

                    Swal.fire({
                        title: `Reseteando clave...`,
                        html: "Porfavor espere...",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    try {

                        toastr.clear();
                        const token = document.querySelector('input[name="_token"]').value;

                        const formData = new FormData();
                        const urlResetPassword = @json(route('landlord.mantenimiento.empresas.resetearClave'));

                        formData.append('company_id', company_id);

                        const response = await fetch(urlResetPassword, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token
                            },
                            body: formData
                        });

                        const res = await response.json();

                        Swal.close();

                        if (response.status === 422) {
                            if ('errors' in res) {
                                //pintarErroresValidacion(res.errors);
                            }
                            Swal.close();
                            return;
                        }

                        if (res.success) {
                            dtCompaniesLandlord.ajax.reload(null, false);
                            toastr.success(res.message, 'OPERACIÓN COMPLETADA');
                            //window.location.href    =   sale_index;
                            Swal.close();
                        } else {
                            toastr.error(res.message, 'ERROR EN EL SERVIDOR');
                            Swal.close();
                        }

                    } catch (error) {
                        toastr.error(error, 'ERROR EN LA PETICIÓN RESETEAR CLAVE');
                    }

                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire({
                        title: "Operación cancelada",
                        text: "No se realizaron acciones",
                        icon: "error"
                    });
                }
            });
        }


        function deleteTenant(company_id) {
            const company = getRowById(dtCompaniesLandlord, company_id);

            let message = `Eliminar empresa: ${company.business_name}-${company.ruc}?`;

            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: "btn btn-success",
                    cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });
            swalWithBootstrapButtons.fire({
                title: message,
                text: "OPERACIÓN NO REVERSIBLE!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar!",
                cancelButtonText: "No, cancelar!",
                reverseButtons: true
            }).then(async (result) => {
                if (result.isConfirmed) {

                    Swal.fire({
                        title: `Eliminando empresa...`,
                        html: "Porfavor espere...",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    try {

                        toastr.clear();
                        const token = document.querySelector('input[name="_token"]').value;

                        const formData = new FormData();
                        const urlDeleteTenant =
                            "{{ route('landlord.mantenimiento.empresas.deleteTenant', ':id') }}".replace(':id',
                                company_id);

                        formData.append('company_id', company_id);

                        const response = await fetch(urlDeleteTenant, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'X-HTTP-Method-Override': 'DELETE'
                            }
                        });

                        const res = await response.json();

                        Swal.close();

                        if (response.status === 422) {
                            if ('errors' in res) {
                                //pintarErroresValidacion(res.errors);
                            }
                            Swal.close();
                            return;
                        }

                        if (res.success) {
                            dtCompaniesLandlord.ajax.reload(null, false);
                            toastr.success(res.message, 'OPERACIÓN COMPLETADA');
                            //window.location.href    =   sale_index;
                            Swal.close();
                        } else {
                            toastr.error(res.message, 'ERROR EN EL SERVIDOR');
                            Swal.close();
                        }

                    } catch (error) {
                        toastr.error(error, 'ERROR EN LA PETICIÓN ELIMINAR EMPRESA');
                    }

                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire({
                        title: "Operación cancelada",
                        text: "No se realizaron acciones",
                        icon: "error"
                    });
                }
            });
        }

        function actionChangeBlockAccount(chkBlockAccount) {
            toastr.clear();
            const companyId = chkBlockAccount.getAttribute('data-company');
            const newState = chkBlockAccount.checked;
            const oldState = !newState;
            chkBlockAccount.checked = oldState;
            const row = getRowById(dtCompaniesLandlord, companyId);

            let message = "";
            let messageProcess = "";
            let html = "";

            if (newState) {
                message = "Desea bloquear la cuenta?";
                messageProcess = 'Bloqueando cuenta...'
            } else {
                message = "Desea activar la cuenta?"
                messageProcess = 'Activando cuenta...'
            }

            const iconAction = newState ?
                '<i class="fas fa-user-lock text-danger fa-3x mb-3"></i>' :
                '<i class="fas fa-user-check text-success fa-3x mb-3"></i>';

            html = `
            <div class="text-center small">

                    ${iconAction}

                            <div class="mb-2">
                                <strong>Empresa:</strong><br>
                                ${row.business_name}
                            </div>

                            <div class="mb-2">
                                <strong>RUC:</strong><br>
                                ${row.ruc}
                            </div>

                            <div class="mb-2">
                                <strong>Dominio:</strong><br>
                                ${row.domain}
                            </div>
                </div>
            `;

            Swal.fire({
                title: message,
                html: html,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí',
                cancelButtonText: 'No',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then(async (result) => {

                if (result.isConfirmed) {

                    try {

                        Swal.fire({
                            title: messageProcess,
                            html: 'Procesando...',
                            icon: 'info',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        const formData = new FormData();
                        formData.append('_method', 'PUT');
                        formData.append('block_account', newState ? 1 : 0);

                        const res = await axios.post(route('landlord.mantenimiento.empresas.bloquearEmpresa',
                            companyId), formData);

                        if (res.data.success) {
                            chkBlockAccount.checked = newState;
                            toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                        } else {
                            toastr.error(res.data.message, 'Error en el servidor al bloquear empresa');
                        }

                    } catch (error) {
                        const message =
                            error.response?.data?.message ||
                            error.message ||
                            'Error inesperado';

                        toastr.error(message, 'Error en la petición bloquear empresa');
                    } finally {
                        Swal.close();
                    }

                } else if (result.dismiss === Swal.DismissReason.cancel) {

                    Swal.fire({
                        icon: 'info',
                        title: 'Operación cancelada',
                        text: 'No se realizaron acciones',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });

                }

            });
        }
    </script>
@endsection
