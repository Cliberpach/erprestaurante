<div class="modal fade" id="mdlOpenCash" tabindex="-1" aria-labelledby="mdlOpenCashLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Aperturar Caja </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12 colCaja">
                            @include('cash.petty-cash-book.forms.form_open_cash')
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row footer-row">

                    <div class="col-12 text-end">
                        <button type="button" class="btn btn-secondary btnCancelar" data-bs-dismiss="modal">
                            <i class="fas fa-ban"></i> Cancelar
                        </button>
                        <button form="form-open-cash" type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                    </div>
                    <div class="col-12">
                        <p>Los campos con asterisco (*) son obligatorios.</p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


<script>
    let dtFreeServers;
    const lstServers = [];

    function openMdlOpenCash() {
        $('#mdlOpenCash').modal('show');
    }

    function eventsMdlOpenCash() {
        loadDtFreeServers();
        document.querySelector('#form-open-cash').addEventListener('submit', (e) => {
            e.preventDefault();
            openPettyCash(e.target);
        })

        document.addEventListener('click', (e) => {

            if (e.target.closest('.chk-server')) {
                actionCheckServer(e.target);
            }

            if (e.target.closest('.btnReloadServers')) {
                reloadServersFree();
            }

        });

        $('#mdlOpenCash').on('hidden.bs.modal', function() {
            reloadServersFree();
            window.cashesAvailableSelect.clear();
            window.cashesAvailableSelect.refreshOptions(false);
        });

    }

    function loadDtFreeServers() {
        const url = @json(route('tenant.utils.getListFreeServers'));

        dtFreeServers = new DataTable('#dt-servers', {
            serverSide: false,
            processing: true,
            ajax: {
                url: url,
                type: 'GET',
                data: function(d) {},
            },
            order: [
                [0, 'desc']
            ],
            columns: [{
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="form-check form-switch text-center">
                                <input
                                    class="form-check-input chk-server"
                                    type="checkbox"
                                    value="${data}"
                                    id="server_${data}"
                                >
                            </div>
                        `;
                    }
                },
                {
                    visible: false,
                    data: 'id',
                    name: 'u.id'
                },
                {
                    data: 'user_name',
                    name: 'u.name'
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

    function openPettyCash(formOpenCash) {
        const id = cashesAvailableSelect.getValue();
        const item = cashesAvailableSelect.options[id];


        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
            },
            buttonsStyling: false
        });

        swalWithBootstrapButtons.fire({
            title: "Desea aperturar la caja?",
            html: `
                <div style="text-align: center; margin-top: 10px;">
                    <p style="font-size: 16px; margin-bottom: 10px;">
                        <strong>Nombre:</strong> ${item.name}
                    </p>
                </div>
            `,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí!",
            cancelButtonText: "No, cancelar!",
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {

                Swal.fire({
                    title: "Aperturando caja...",
                    text: "Por favor, espere",
                    icon: "info",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    toastr.clear();

                    const formData = new FormData(formOpenCash);
                    formData.append('lst_servers', JSON.stringify(lstServers));
                    const res = await axios.post(route('tenant.movimientos_caja.abrirCaja'), formData);

                    if (res.data.success) {
                        toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                        $('#mdlOpenCash').modal('hide');
                        dtCash.ajax.reload();
                    } else {
                        Swal.close();
                        toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                    }

                } catch (error) {
                    if (error.response) {
                        if (error.response.status === 422) {
                            const errors = error.response.data.errors;
                            paintValidationErrors(errors, 'error');
                            Swal.close();
                            toastr.error('Errores de validación encontrados.', 'ERROR DE VALIDACIÓN');
                        } else {
                            Swal.close();
                            toastr.error(error.response.data.message, 'ERROR EN EL SERVIDOR');
                        }
                    } else if (error.request) {
                        Swal.close();
                        toastr.error('No se pudo contactar al servidor. Revisa tu conexión a internet.',
                            'ERROR DE CONEXIÓN');
                    } else {
                        Swal.close();
                        toastr.error(error.message, 'ERROR DESCONOCIDO');
                    }
                } finally {
                    Swal.close();
                }

            } else if (result.dismiss === Swal.DismissReason.cancel) {
                swalWithBootstrapButtons.fire({
                    title: "Operación cancelada",
                    text: "No se realizaron acciones",
                    icon: "error"
                });
            }
        });
    }

    function actionCheckServer(chkServer) {
        const serverId = chkServer.value;
        if (chkServer.checked) {
            lstServers.push(serverId);
        } else {
            const index = lstServers.indexOf(serverId);
            if (index > -1) {
                lstServers.splice(index, 1);
            }
        }
    }

    function reloadServersFree() {
        toastr.clear();
        dtFreeServers.ajax.reload();
        lstServers.length = 0;
        toastr.info('MESEROS RECARGADOS', 'SE LIMPIARON SELECCIONES');
    }
</script>
