<div class="modal fade" id="modal_resumenes" tabindex="-1" aria-labelledby="modalResumenesLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content rounded-3 shadow-lg">
            <!-- Header -->
            <div class="modal-header d-flex flex-column align-items-center py-3" style="background-color: white;">
                <i class="fas fa-book fs-1" style="font-size: 100px !important;color:rgb(1, 68, 140);"></i>
                <h5 class="modal-title fw-bold mt-2" id="modalResumenesLabel"
                    style="font-family: 'Helvetica', 'Arial', sans-serif;">RESÚMENES</h5>
                <button type="button" class="btn-close btn-close-dark position-absolute end-0 me-3"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body content_cliente">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input id="fecha_comprobante" type="date" class="form-control shadow-sm"
                                value="<?php echo date('Y-m-d'); ?>">
                            <label for="fecha_comprobante" class="text-dark" style="font-weight:bold;">Fecha de Emisión
                                de comprobantes</label>
                        </div>
                    </div>
                    <div class="col-md-8 d-flex align-items-end">
                        <button class="btn btn-primary shadow-sm" id="btn-get-comprobantes">
                            <i class="fas fa-search"></i> Buscar comprobantes
                        </button>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col">
                        <div class="table-responsive rounded-3 border p-2 shadow-sm">
                            @include('sales.summaries.tables.tbl_add_resumenes')
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer d-flex justify-content-between bg-light">
                <div>
                    <i class="fa fa-exclamation-circle text-danger"></i>
                    <small class="text-muted">Los campos marcados con asterisco (*) son obligatorios.</small>
                </div>
                <div>
                    <button type="submit" class="btn btn-success btn-sm btn-guardar-resumen shadow-sm">
                        <i class="fa fa-save"></i> Guardar
                    </button>
                    <button type="button" class="btn btn-danger btn-sm shadow-sm" data-bs-dismiss="modal">
                        <i class="fa fa-times"></i> Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    let dtInvoices = null;

    function eventsMdlCreateResumen() {

        //====== OBTENER COMPROBANTES BOLETAS ======
        document.querySelector('#btn-get-comprobantes').addEventListener('click', () => {
            //===== OBTENIENDO FECHA DEL INPUT DATE =====
            const fechaComprobantes = document.querySelector('#fecha_comprobante').value;
            fecha_comprobantes = fechaComprobantes;

            if (fechaComprobantes) {
                getComprobantes(fechaComprobantes);
            } else {
                toastr.error('DEBE SELECCIONAR UNA FECHA');
            }
        })

        $('#modal_resumenes').on('hidden.bs.modal', function() {

            listComprobantes = [];

            destroyDataTable(dtInvoices);
            clearTable('tbl_add_resumenes');
            pintarTableComprobantes();
            dtInvoices = loadDataTableSimple('tbl_add_resumenes');

        });
    }

    //============= ABRIR MODAL CLIENTE =============
    async function openModalResumenes() {
        $("#modal_resumenes").modal("show");
    }

    //===== OBTENER COMPROBANTES =====
    async function getComprobantes(fechaComprobantes) {
        try {
            toastr.clear();
            mostrarAnimacion1();

            const url = '{{ route('tenant.ventas.resumenes.getInvoices', ['fecha' => '__FECHA__']) }}'.replace(
                '__FECHA__', fechaComprobantes);
            const res = await axios.get(url);

            console.log(res);

            if (res.data.success) {

                listComprobantes = res.data.comprobantes;
                destroyDataTable(dtInvoices);
                clearTable('tbl_add_resumenes');
                pintarTableComprobantes();
                dtInvoices = loadDataTableSimple('tbl_add_resumenes');

                toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
            } else {
                toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
            }

        } catch (error) {
            console.error(error.message, 'ERROR EN LA PETICIÓN OBTENER COMPROBANTES');
        } finally {
            ocultarAnimacion1();
        }
    }


    //====== PINTAR TABLE COMPROBANTES =====
    function pintarTableComprobantes() {

        const bodyTableSearchComprobantes = document.querySelector('#tbl_add_resumenes tbody');

        let filas = ``;
        listComprobantes.forEach((c) => {
            filas += `<tr>
                            <th scope="row">${c.documento_id}</th>
                            <td>${c.documento_serie}-${c.documento_correlativo}</td>
                            <td>${c.documento_moneda}</td>
                            <td>${c.documento_porcentaje_igv}</td>
                            <td>${c.documento_subtotal}</td>
                            <td>${c.documento_igv}</td>
                            <td>${c.documento_total}</td>
                            <td>
                                <i class="btn btn-danger fas fa-trash-alt btn-delete-document" data-documento-id=${c.documento_id}></i>
                            </td>
                        </tr>
                        `;
        })

        bodyTableSearchComprobantes.innerHTML = filas;
    }
</script>
