<div class="modal fade" id="mdl_convert_sale" tabindex="-1" aria-labelledby="mdl_convert_sale_label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <i class="fa fa-cogs text-primary me-2"></i>
                <div>
                    <h5 class="modal-title mb-0" id="mdl_convert_sale_label">Convertir Documento Venta</h5>
                    <small class="text-muted"></small>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                @include('sales.sale_document.forms.form_convert_sale')
            </div>

            <!-- Footer -->
            <div class="modal-footer d-flex justify-content-end flex-wrap">
                <button type="button" class="btn btn-secondary btn-sm me-2" data-bs-dismiss="modal">
                    <i class="fa fa-times"></i> Cerrar
                </button>
                <button type="submit" form="form-convert-sale" class="btn btn-primary btn-sm me-2">
                    <i class="fas fa-save"></i> Convertir
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
        const paramsMdlConvert = {
            id: null,
        };

        async function openMdlConvert(id) {
            paramsMdlConvert.id = id;
            setFormConvert();
            const modal = new bootstrap.Modal(document.getElementById('mdl_convert_sale'));
            modal.show();
        }

        function eventsMdlConvert() {
            loadSelectMdlConvert();

            document.querySelector('#form-convert-sale').addEventListener('submit', (e) => {
                actionFormConvert(e);
            })
        }

        function loadSelectMdlConvert() {
            const initialCustomer = @json($customer_formatted);
            window.customerSelectMc = new TomSelect('#customer_id_mc', {
                valueField: 'id',
                options: [initialCustomer],
                items: [initialCustomer.id],
                labelField: 'full_name',
                searchField: ['full_name'],
                plugins: ['clear_button'],
                placeholder: 'Seleccione un cliente',
                maxOptions: 20,
                create: false,
                preload: false,
                onType: (str) => {
                    lastCustomerQuery = str;
                },
                load: async (query, callback) => {
                    if (query.length < 3) return callback();
                    try {
                        const url = `{{ route('tenant.utils.searchCustomer') }}?q=${encodeURIComponent(query)}`;
                        const response = await fetch(url);
                        if (!response.ok) throw new Error('Error al buscar clientes');
                        const data = await response.json();
                        const results = data.data ?? [];
                        callback(results);
                        if (results.length === 0) {
                            customerParams.documentSearchCustomer = lastCustomerQuery;
                            console.log("No se encontró en BD. Guardado:", window.typedCustomer);
                        }
                    } catch (error) {
                        console.error('Error cargando clientes:', error);
                        callback();
                    }
                },
                render: {
                    option: (item, escape) => `
                        <div>
                            <strong>${escape(item.full_name)}</strong><br>
                            <small>${escape(item.email ?? '')}</small>
                        </div>
                    `,
                    item: (item, escape) => `<div>${escape(item.full_name)}</div>`,
                    no_results: function(data, escape) {
                        return `
                            <div class="no-results">
                                <i class="fas fa-search" style="margin-right:6px; color:#17a2b8;"></i>
                                Sin resultados
                            </div>
                        `;
                    }
                }
            });
        }

        async function actionFormConvert(e) {
            e.preventDefault();
            const row = getRowById(dtSales, paramsMdlConvert.id);

            const invoiceSelect = document.querySelector('#invoice_id_mc');
            const selectedOption = invoiceSelect.options[invoiceSelect.selectedIndex];
            const invoiceName = selectedOption?.text || 'documento';
            let message = `Convertir documento: ${row.serie}-${row.correlative} a ${invoiceName}?`;

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
                        title: `Venta: ${row.serie}-${row.correlative}`,
                        html: `Convirtiendo a ${invoiceName}...`,
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    try {
                        toastr.clear();

                        const formData = new FormData(e.target);
                        const url = route('tenant.ventas.comprobante_venta.convert');

                        formData.append('sale_id', paramsMdlConvert.id);

                        const res = await axios.post(url, formData);
                        if (res.data.success) {
                            toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                            dtSales.ajax.reload(null, false);
                            $('#mdl_convert_sale').modal('hide');
                        } else {
                            toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                        }

                    } catch (error) {
                        toastr.error(error, 'ERROR EN LA PETICIÓN CONVERTIR VENTA');
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

        function setFormConvert() {
            const row = getRowById(dtSales, paramsMdlConvert.id);
            const fullName = row.customer_full_name;
            const id = row.customer_id;


            const customer = {
                id: row.customer_id,
                full_name: row.customer_full_name
            };

            window.customerSelectMc.clear();
            window.customerSelectMc.clearOptions();
            window.customerSelectMc.addOption(customer);
            window.customerSelectMc.setValue(customer.id, true);
        }
    </script>
@endpush
