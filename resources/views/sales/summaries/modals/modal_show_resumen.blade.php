<div class="modal fade" id="modal_show_resumen" tabindex="-1" aria-labelledby="modalShowResumenLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-width: 80%;" role="document">
        <div class="modal-content rounded-3 shadow-lg">
            <!-- Header -->
            <div class="modal-header d-flex align-items-center text-white">
                <h5 class="modal-title" id="modalShowResumenLabel">
                    <i class="fas fa-info-circle modal-icon fs-3 me-2"></i> DETALLE RESUMEN
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <!-- Body -->
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col">
                        @include('sales.summaries.lists.lst_resumen_master')
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            @include('sales.summaries.tables.tbl_show_resumenes')
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer -->
            <div class="modal-footer d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fa fa-exclamation-circle text-warning me-2"></i>
                    <small class="text-muted">Los campos marcados con asterisco (*) son obligatorios.</small>
                </div>
                <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">
                    <i class="fa fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>



<script>
    let dtShowResumenes = null;

    function openMdlShowResumen(resumen_id) {
        getResumenes(resumen_id);
    }

    async function getResumenes(resumen_id) {
        try {
            toastr.clear();
            mostrarAnimacion1();

            const url = '{{ route('tenant.ventas.resumenes.show', ['resumen_id' => '__ID__']) }}'.replace('__ID__',
                resumen_id);
            const res = await axios.get(url);

            if (res.data.success) {

                pintarResumenMaster(res.data.resumen);

                destroyDataTable(dtShowResumenes);
                clearTable('tbl_show_resumenes');
                pintarResumenDetalle(res.data.resumen, res.data.detalle);
                dtShowResumenes = loadDataTableSimple('tbl_show_resumenes');

                toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                $('#modal_show_resumen').modal('show');
            } else {
                toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
            }
        } catch (error) {
            toastr.error(error.message, 'ERROR EN LA PETICIÓN OBTENER RESUMEN');
        } finally {
            ocultarAnimacion1();
        }
    }

    function pintarResumenMaster(resumen) {
        for (const [key, value] of Object.entries(resumen)) {

            if (key === 'ruta_cdr' || key === 'ruta_xml') {
                continue;
            }

            const element = document.getElementById(key);
            if (element) {
                element.textContent = value;
            }
        }
    }

    function pintarResumenDetalle(resumen, detalle) {

        const tbodyResumenDetalle = document.querySelector('#tbl_show_resumenes tbody');

        tbodyResumenDetalle.innerHTML = '';

        let filas = ``;
        detalle.forEach((d) => {

            filas += `
                        <tr>
                            <td>${d.documento_id}</td>
                            <td>${d.documento_serie}-${d.documento_correlativo}</td>
                            <td>${d.documento_nombre_cliente}</td>
                            <td>${d.documento_doc_cliente}</td>
                            <td>${d.documento_porcentaje_igv}</td>
                            <td>${d.documento_total}</td>
                            <td>${d.documento_subtotal}</td>
                            <td>${d.documento_igv}</td>
                            <td>${resumen.date_invoices}</td>
                        </tr>
                    `;

        })

        tbodyResumenDetalle.innerHTML = filas;

    }
</script>
