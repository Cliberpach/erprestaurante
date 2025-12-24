<div class="modal fade" id="mdlEditBook" tabindex="-1" aria-labelledby="mdlEditBookLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Editar Movimiento Caja </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12 colCaja">
                            @include('cash.petty-cash-book.forms.form_edit_book')
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
                        <button form="form-edit-book" type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar
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
    let dtFreeServersEdit;
    const lstServersEdit = [];
    const paramsMdlEditBook = {
        id: null,
    };

    async function openMdlEditBook(id) {
        paramsMdlEditBook.id = id;
        await getOne();
        $('#mdlEditBook').modal('show');
    }

    async function getOne() {

        try {
            mostrarAnimacion1();
            const res = await axios.get(route('tenant.movimientos_caja.getOne', {
                id: paramsMdlEditBook.id
            }));
            if (res.data.success) {

                setEditBook(res.data.data);
                dtFreeServersEdit.ajax.reload(null, false);

            } else {
                toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
            }
        } catch (error) {
            toastr.error(error, 'ERROR EN LA PETICIÓN OBTENER MOVIMIENTO DE CAJA');
        } finally {
            ocultarAnimacion1();
        }

    }

    function setEditBook(data) {
        let movimientoId = data.petty_cash_book.id;
        let codigoFormateado = 'CM-' + String(movimientoId).padStart(8, '0');
        document.querySelector('#infoCodigoCaja').textContent = codigoFormateado;
        document.querySelector('#infoNombreCaja').textContent = data.petty_cash_book.petty_cash_name;

        document.getElementById('shift_edit').value = data.petty_cash_book.shift_id;
        document.getElementById('initial_amount_edit').value = parseFloat(data.petty_cash_book.initial_amount).toFixed(
            2);

        lstServersEdit.length = 0;
        data.servers.forEach(server => {
            lstServersEdit.push(server.user_id.toString());
        });

    }

    function eventsMdlEditBook() {
        loadDtEditServers();

        document.querySelector('#form-edit-book').addEventListener('submit', (e) => {
            e.preventDefault();
            updatePettyCash(e.target);
        })

        document.addEventListener('click', (e) => {

            if (e.target.closest('.chk-server-edit')) {
                actionCheckServerEdit(e.target);
            }

            if (e.target.closest('.btnReloadServersEdit')) {
                reloadServersFreeEdit();
            }

        });

        $('#mdlEditBook').on('hidden.bs.modal', function() {
            reloadServersFreeEdit();
        });

    }

    function loadDtEditServers() {
        const url = @json(route('tenant.utils.getListFreeServers'));

        dtFreeServersEdit = new DataTable('#dt-servers-edit', {
            serverSide: false,
            processing: true,
            ajax: {
                url: url,
                type: 'GET',
                data: function(d) {
                    d.id = paramsMdlEditBook.id;
                },
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
                                    class="form-check-input chk-server-edit"
                                    type="checkbox"
                                    value="${data}"
                                    id="server_edit_${data}"
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
            },
            drawCallback: function() {
                lstServersEdit.forEach(function(id) {
                    const checkbox = document.querySelector(`#server_edit_${id}`);
                    console.log('check', checkbox)
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });
            }
        });
    }

    function updatePettyCash(formOpenCash) {

        const fila = getRowById(dtCash, paramsMdlEditBook.id);

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
            },
            buttonsStyling: false
        });

        swalWithBootstrapButtons.fire({
            title: "Desea actualizar la caja?",
            html: `
                <div style="text-align: center; margin-top: 10px;">
                    <p style="font-size: 16px; margin-bottom: 10px;">
                        <strong>N°:</strong> ${fila.code}
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
                    title: "Actualizando caja...",
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
                    formData.append('_method', 'PUT');
                    formData.append('lst_servers', JSON.stringify(lstServersEdit));

                    const res = await axios.post(route('tenant.movimientos_caja.update', {
                        id: paramsMdlEditBook.id
                    }), formData);

                    if (res.data.success) {
                        toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                        $('#mdlEditBook').modal('hide');
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

    function actionCheckServerEdit(chkServer) {
        const serverId = chkServer.value;
        if (chkServer.checked) {
            lstServersEdit.push(serverId);
        } else {
            const index = lstServersEdit.indexOf(serverId);
            if (index > -1) {
                lstServersEdit.splice(index, 1);
            }
        }
    }

    function reloadServersFreeEdit() {
        toastr.clear();
        dtFreeServersEdit.ajax.reload();
        toastr.info('MESEROS RECARGADOS', 'SE LIMPIARON SELECCIONES');
    }
</script>
