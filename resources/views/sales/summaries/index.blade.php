@extends('layouts.template')

@section('title')
    Lista de Resúmenes
@endsection

@section('content')
    @include('sales.summaries.modals.modal_create_resumen')
    @include('sales.summaries.modals.modal_show_resumen')

    <div class="card overflow-hidden">
        <div class="card-header">

            <div class="row mb-2 justify-between">
                <div class="col-6">
                    <h6 class="card-title mb-0">LISTA DE RESÚMENES</h6>
                </div>
                <div class="col-6" style="text-align: end;">
                    <div class="input-group-append">
                        <button onclick="openModalResumenes()" type="button" class="btn btn-primary">
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
            <div class="table-responsive">
                @include('sales.summaries.tables.tbl_list')
            </div>
        </div>
    </div>
@endsection


@section('js')
    <script>
        let tableResumenes = null;

        let fecha_comprobantes = null;
        let listComprobantes = [];

        document.addEventListener('DOMContentLoaded', () => {
            events();
            cargarDataTableResumenes();
            dtInvoices = loadDataTableSimple('tbl_add_resumenes');
            dtShowResumenes = loadDataTableSimple('tbl_show_resumenes');
        })

        function events() {
            eventsMdlCreateResumen();

            //====== CONSULTAR RESUMEN =======
            document.addEventListener('click', (e) => {
                if (e.target.classList.contains('btn-consultar-resumen')) {
                    const resumen_id = e.target.getAttribute('data-resumen-id');
                    consultarResumen(resumen_id);
                }
                if (e.target.classList.contains('btn-reenviar-resumen')) {
                    const resumen_id = e.target.getAttribute('data-resumen-id');

                    enviarResumen(resumen_id);
                }
                if (e.target.classList.contains('btn-detalle-resumen')) {
                    const resumen_id = e.target.getAttribute('data-resumen-id');
                    openMdlShowResumen(resumen_id);
                }
            })

            //======= GUARDAR RESUMEN ======
            document.addEventListener('click', (e) => {
                if (e.target.classList.contains('btn-guardar-resumen')) {

                    const valido = validaciones();

                    if (valido) {
                        Swal.fire({
                            title: "Desea registrar y enviar un nuevo resúmen de boletas?",
                            text: "Acción no reversible!",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonText: "Sí!",
                            cancelButtonText: "No, cancelar!",
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {

                                Swal.fire({
                                    title: 'REGISTRANDO RESUMEN Y ENVIANDO A SUNAT...',
                                    text: 'Por favor, espere mientras se procesa la solicitud.',
                                    allowOutsideClick: false, // Prevenir cerrar el modal
                                    didOpen: () => {
                                        Swal.showLoading(); // Mostrar el loader
                                    }
                                });

                                saveSendResumen();
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
                }
            })

            //===== ELIMINAR COMPROBANTE DEL CARRITO ======
            document.addEventListener('click', (e) => {
                if (e.target.classList.contains('btn-delete-document')) {

                    const documento_id = e.target.getAttribute('data-documento-id');
                    eliminarComprobante(documento_id);

                    destroyDataTable(dtInvoices);
                    clearTable('tbl_add_resumenes');
                    pintarTableComprobantes();
                    dtInvoices = loadDataTableSimple('tbl_add_resumenes');

                }
            })

        }

        //====== REENVIAR RESUMEN ========
        async function enviarResumen(resumen_id) {

            try {
                mostrarAnimacion1();

                const url = '{{ route('tenant.ventas.resumenes.enviarSunat') }}';
                const res = await axios.post(url, {
                    'resumen_id': resumen_id
                });

                console.log(res);
                if (res.data.success) {
                    tableResumenes.ajax.reload();
                    toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                } else {
                    toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                }

            } catch (error) {

                toastr.error(error.response.data, 'ERROR EN LA PETICIÓN ENVIAR RESUMEN A SUNAT', {
                    timeOut: 0
                });
            } finally {
                ocultarAnimacion1();
            }
        }

        //======== CONSULTAR RESUMEN ======
        async function consultarResumen(resumen_id) {
            try {
                toastr.clear();
                mostrarAnimacion1();

                const url = '{{ route('tenant.ventas.resumenes.consultar') }}';

                const res = await axios.post(url, {
                    'resumen_id': resumen_id
                });

                console.log(res);

                if (res.data.success) {
                    tableResumenes.ajax.reload(null, false);
                    toastr.success(res.data.message, 'CONSULTA COMPLETADA', {
                        timeOut: 0
                    });
                } else {
                    toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                }

            } catch (error) {
                toastr.error(error.message, 'ERROR EN LA PETICIÓN CONSULTAR RESUMEN');
            } finally {
                ocultarAnimacion1();
            }
        }

        //========== ACTUALIZAR DATATABLE ==============
        function actualizarDataTable(resumen_id, res_consulta) {

            const rowIndex = tableResumenes.rows().indexes().filter(function(index) {
                return tableResumenes.row(index).data().id == resumen_id;
            });

            if (rowIndex.length > 0) {
                tableResumenes.row(rowIndex[0]).data(res_consulta).draw();
            } else {
                toastr.error('NO SE ENCONTRÓ LA FILA CON LOS DATOS RESPECTIVOS', 'RECARGUE LA PÁGINA PARA CORREGIR');
            }
            //tableResumenes.row().data(res_consulta).draw();
        }

        function cargarDataTableResumenes() {
            const url = "{{ route('tenant.ventas.resumenes.getAll') }}";

            tableResumenes = new DataTable('#tbl_list_resumenes', {
                serverSide: true,
                processing: true,
                ajax: {
                    url: url,
                    type: 'GET'
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: 'date_invoices'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return data.serie + '-' + data.correlative;
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            let badgeContent = '';

                            //======= ENVIADO A SUNAT ======
                            if (data.send_sunat == 1) {
                                //===== ACEPTADO POR SUNAT =====
                                if (data.status_result_code == '0') {
                                    badgeContent = `<span class="badge bg-success">ACEPTADO</span>`;
                                }
                                //====== RESPUESTA DE SUNAT "ERRORES EN EL ARCHIVO" ====
                                if (data.status_result_code == 99) {
                                    badgeContent =
                                        `<span class="badge bg-danger">ENVIADO CON ERRORES</span>`;
                                }
                                //===== EN PROCESO =====
                                if (data.status_result_code == 98) {
                                    badgeContent = `<span class="badge bg-warning">EN PROCESO</span>`;
                                }
                                if (data.status_result_code != 0 && data.status_result_code != 99 && data
                                    .status_result_code != 98) {
                                    badgeContent = `<span class="badge bg-primary">ENVIADO</span>`;
                                }
                            }

                            //====== AÚN NO ENVIADO A SUNAT =====
                            if (data.send_sunat == 0) {
                                //======== ERRORES HTTP,ETC ======
                                if (!data.summary_result_ticket) {
                                    badgeContent = `<span class="badge bg-danger">ERROR AL ENVIAR</span>`;
                                }
                            }

                            return `<div class="text-center">${badgeContent}</div>`;
                        }
                    },
                    {
                        data: 'summary_result_ticket',
                        title: 'TICKET'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            var html =
                                `<td style="white-space: nowrap;"><div style="display: flex; justify-content: center;">`;

                            if (data.route_xml) {
                                let urlGetXml =
                                    "{{ route('tenant.ventas.resumenes.getXml', ['resumen_id' => ':resumen_id']) }}";
                                urlGetXml = urlGetXml.replace(':resumen_id', data.id);

                                html += `<form action="${urlGetXml}" method="get">`;
                                html +=
                                    `<button type="submit" class="btn btn-primary btn-xml">XML</button>`;
                                html += `</form>`;
                            }

                            if (data.route_cdr) {
                                let urlGetCdr =
                                    "{{ route('tenant.ventas.resumenes.getCdr', ['resumen_id' => ':resumen_id']) }}";
                                let url_getCdr = urlGetCdr.replace(':resumen_id', data.id);


                                html +=
                                    `<form style="margin-left:3px;" action="${url_getCdr}" method="get">`;
                                html +=
                                    `<button type="submit" class="btn btn-primary btn-xml">CDR</button>`;
                                html += `</form>`;
                            }

                            html += `</div></td>`;

                            return html;
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {

                            let html = '<div class="btn-group" role="group">';

                            // 🔄 CONSULTAR
                            if (data.send_sunat == 1) {
                                if (data.status_result_code == 98 || (data.summary_result_ticket && !data
                                        .status_result_code)) {
                                    html += `
                                        <button type="button"
                                            data-resumen-id="${data.id}"
                                            class="btn btn-primary btn-sm btn-consultar-resumen">
                                            <i class="fas fa-search me-1"></i> Consultar
                                        </button>
                                    `;
                                }
                            }

                                                // 📤 ENVIAR
                                                if (data.send_sunat == 0 && !data.summary_result_ticket) {
                                                    html += `
                                    <button type="button"
                                        data-resumen-id="${data.id}"
                                        class="btn btn-success btn-sm btn-reenviar-resumen">
                                        <i class="fas fa-paper-plane me-1"></i> Enviar
                                    </button>
                                `;
                                                }

                                                // 👁 VER DETALLE
                                                html += `
                                <button type="button"
                                    data-resumen-id="${data.id}"
                                    class="btn btn-info btn-sm btn-detalle-resumen">
                                    <i class="fas fa-eye"></i>
                                </button>
                            `;

                            html += '</div>';

                            return html;
                        }
                    }
                ],
                language: {
                    processing: "Cargando resúmenes",
                    search: "BUSCAR: ",
                    lengthMenu: "MOSTRAR _MENU_ RESÚMENES",
                    info: "MOSTRANDO _START_ A _END_ DE _TOTAL_ RESÚMENES",
                    infoEmpty: "MOSTRANDO 0 RESÚMENES",
                    infoFiltered: "(FILTRADO de _MAX_ RESÚMENES)",
                    infoPostFix: "",
                    loadingRecords: "CARGA EN CURSO",
                    zeroRecords: "Aucun &eacute;l&eacute;ment &agrave; afficher",
                    emptyTable: "NO HAY RESÚMENES DISPONIBLES",
                    paginate: {
                        first: "PRIMERO",
                        previous: "ANTERIOR",
                        next: "SIGUIENTE",
                        last: "ÚLTIMO"
                    },
                    aria: {
                        sortAscending: ": activer pour trier la colonne par ordre croissant",
                        sortDescending: ": activer pour trier la colonne par ordre décroissant"
                    }
                },
                "order": [
                    [0, "desc"]
                ]
            });
        }

        //========= GUARDAR RESUMEN Y ENVIAR A SUNAT A LA VEZ ==========
        async function saveSendResumen() {

            try {
                const url = '{{ route('tenant.ventas.resumenes.store') }}';
                const res = await axios.post(url, {
                    'comprobantes': JSON.stringify(listComprobantes),
                    'fecha_comprobantes': fecha_comprobantes
                });

                if (res.data.success) {
                    tableResumenes.ajax.reload(null, false);
                    $('#modal_resumenes').modal('hide');
                    toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                } else {
                    toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                }

            } catch (error) {
                toastr.error(error.message, 'ERROR EN LA PETICIÓN GUARDAR Y ENVIAR RESÚMEN', {
                    timeOut: 0
                });

            } finally {
                Swal.close();
                $("#modal_resumenes").modal("hide");
            }
        }


        //==== ELIMINAR COMPROBANTE DEL CARRITO ======
        function eliminarComprobante(documento_id) {
            listComprobantes = listComprobantes.filter((c) => {
                return c.documento_id != documento_id;
            })
        }


        //====== VERIFICAR SI ESTAN ACTIVOS LOS RESÚMENES =======
        async function isActive() {
            try {
                const url = `/ventas/resumenes/getStatus`;
                const response = await axios.get(url);

                if (response.status === 200) {
                    const resumenActive = response.data.resumenActive;
                    //====== VERIFICANDO SI LOS RESUMENES ESTÁN ACTIVOS =====
                    if (!resumenActive) {
                        toastr.error('DEBE ACTIVAR LOS RESÚMENES', 'RESUMEN INACTIVO');
                        return false;
                    }
                    if (resumenActive) {
                        toastr.success('RESÚMENES REGISTRADOS EN LA EMPRESA', 'RESUMEN ACTIVO');
                        return true;
                    }
                } else {
                    toastr.error(response.statusText, 'ERROR EN LA SOLICITUD');
                }

            } catch (error) {
                console.error('Error al verificar estado: ', error);
            }
        }

        function validaciones() {

            let validacion = true;

            //======= VALIDAR DETALLE DEL RESUMEN =======
            if (listComprobantes.length == 0) {
                toastr.error('NO HAY COMPROBANTES EN EL RESUMEN', 'ERROR');
                validacion = false;
            }

            return validacion;

        }
    </script>
@endsection
