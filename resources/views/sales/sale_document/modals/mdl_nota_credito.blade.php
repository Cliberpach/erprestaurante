<div class="modal fade" id="mdl_nc" tabindex="-1" aria-labelledby="mdl_nc_label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <i class="fa fa-cogs text-primary me-2"></i>
                <div>
                    <h5 class="modal-title mb-0" id="mdl_nc_label">Anulación de Documento</h5>
                    <small class="text-muted"></small>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                @include('sales.sale_document.forms.form_nota_credito')
            </div>

            <!-- Footer -->
            <div class="modal-footer d-flex justify-content-end flex-wrap">
                <button type="button" class="btn btn-secondary btn-sm me-2" data-bs-dismiss="modal">
                    <i class="fa fa-times"></i> Cerrar
                </button>
                <button type="submit" form="formNotaCredito" class="btn btn-primary btn-sm me-2">
                    <i class="fas fa-save"></i> Generar
                </button>
            </div>

        </div>
    </div>
</div>


<style>
    .swal2-container {
        z-index: 9999999;
    }
</style>

@push('js-script')
    <script>
        const paramsMdlNc = {
            id: null
        };

        function openMdlNotaCredito(saleId) {
            paramsMdlNc.id = saleId;
            const row = getRowById(dtSales, saleId);
            fillNotaCreditoForm(row);
            $('#mdl_nc').modal('show');
        }

        function eventsMdlNc() {
            document.querySelector('#formNotaCredito').addEventListener('submit', (e) => {
                e.preventDefault();
                storeNc(e.target);
            })

            $('#mdl_nc').on('hidden.bs.modal', function() {
                clearMdlNc();
            });
        }

        function fillNotaCreditoForm(data) {

            document.getElementById('nc_customer_name').textContent = data.customer_name;
            document.getElementById('nc_customer_doc').textContent = data.customer_full_name;
            document.getElementById('nc_doc').textContent = data.doc;

            document.getElementById('nc_type_sale').textContent = data.type_sale_name;
            document.getElementById('nc_serie').textContent = data.serie;
            document.getElementById('nc_correlative').textContent = data.correlative;

            document.getElementById('nc_petty_cash').textContent = data.petty_cash_name;

            document.getElementById('nc_subtotal').textContent = data.subtotal;
            document.getElementById('nc_igv_percent').textContent = data.igv_percentage;
            document.getElementById('nc_igv_amount').textContent = data.igv_amount;
            document.getElementById('nc_total').textContent = data.total;

            // Badges
            setBadge('nc_sunat_status', data.sunat_status);
            setBadge('nc_status', data.status);
            setBadge('nc_pay_status', data.pay_status);
        }

        function setBadge(id, value) {
            const el = document.getElementById(id);
            el.textContent = value;

            el.classList.remove('bg-secondary', 'bg-success', 'bg-danger', 'bg-warning');

            if (value === 'ACEPTADO' || value === 'PAGADO') {
                el.classList.add('bg-success');
            } else if (value === 'RECHAZADO' || value === 'ANULADO') {
                el.classList.add('bg-danger');
            } else {
                el.classList.add('bg-warning');
            }
        }

        function storeNc(formNc) {

            const sale = getRowById(dtSales, paramsMdlNc.id);

            let message = `Anular venta: ${sale.serie}-${sale.correlative} en Sunat?`;

            Swal.fire({
                title: message,
                text: "OPERACIÓN NO REVERSIBLE!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí!",
                cancelButtonText: "No, cancelar!",
                reverseButtons: true
            }).then(async (result) => {
                if (result.isConfirmed) {

                    Swal.fire({
                        title: `Anulando: ${sale.serie}-${sale.correlative}`,
                        html: "Generando nota de crédito...",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    try {
                        toastr.clear();

                        const formData = new FormData(formNc);
                        const url = @json(route('tenant.ventas.comprobante_venta.annular'));

                        formData.append('sale_id', paramsMdlNc.id);

                        const res = await axios.post(url, formData);

                        Swal.close();

                        if (res.data.success) {
                            dtSales.ajax.reload(null, false);
                            toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                            Swal.close();
                            $('#mdl_nc').modal('hide');
                        } else {
                            toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                            Swal.close();
                        }

                    } catch (error) {
                        if (error.response && error.response.status === 422) {
                            paintValidationErrors(error.response.data.errors, 'error');
                            toastr.warning('Corrige los errores del formulario.', 'VALIDACIÓN');
                            Swal.close();
                            return;
                        }
                        toastr.error(
                            error.response?.data?.message ?? 'Ocurrió un error inesperado.',
                            'ERROR EN LA PETICIÓN GENERAR NOTA CRÉDITO'
                        );
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

        function clearMdlNc(){
            document.querySelector('#motive').value =   '';
        }
    </script>
@endpush
