<div class="modal fade" id="mdlDeleteOrder" tabindex="-1" aria-labelledby="mdlDeleteOrderLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow">

            <!-- HEADER -->
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2" id="mdlDeleteOrderLabel">
                    <i class="fas fa-trash-can"></i>
                    Eliminar pedido: <span id="spanOrderDelete" class="fw-semibold"></span>
                </h5>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body">
                @include('waiter_counter.counter.forms.form_delete_order')
            </div>

            <div class="modal-footer">
                <button class="btn btn-danger" type="submit" form="form-delete-order" id="btn-change-tbl">
                    <i class="fas fa-save"></i> Eliminar
                </button>
            </div>

        </div>
    </div>
</div>



@push('js-script')
    <script>
        const paramsMdlDeleteOrder = {
            orderId: null,
            tableSelected: {
                id: null,
                name: null
            }
        }

        function eventsMdlDeleteOrder() {
            document.querySelector('#form-delete-order').addEventListener('submit', (e) => {
                e.preventDefault();
                deleteOrder(e.target);
            })
        }

        async function openMdlDeleteOrder(orderId) {
            paramsMdlDeleteOrder.orderId = orderId;
            document.querySelector('#spanOrderDelete').textContent = paramsMdlShow.order.order_code;
            $('#mdlDeleteOrder').modal('show');
        }

        function deleteOrder(formDeleteOrder) {
            toastr.clear();
            Swal.fire({
                title: `Eliminar pedido: ${paramsMdlShow.order.order_code}?`,
                text: "Se devolverán stocks de platos y productos!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí!",
                cancelButtonText: "No, cancelar!",
                reverseButtons: true
            }).then(async (result) => {
                if (result.isConfirmed) {

                    Swal.fire({
                        title: `Eliminando pedido ${paramsMdlShow.order.order_code}`,
                        html: `Cargando...`,
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    try {

                        const formData = new FormData(formDeleteOrder);
                        formData.append('_method', 'PUT');
                        let url = route('tenant.mostrador_mesero.mostrador.destroy', paramsMdlDeleteOrder
                            .orderId);

                        const res = await axios.post(url, formData);

                        if (res.data.success) {
                            loadTablesAsCircles();
                            toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                            $('#mdlDeleteOrder').modal('hide');
                            $('#mdlShowOrder').modal('hide');
                        } else {
                            toastr.error(res.data.message, 'ERROR EN EL SERVIDOR ELIMINAR PEDIDO');
                        }

                    } catch (error) {
                        toastr.error(error, 'ERROR EN LA PETICIÓN ELIMINAR PEDIDO');
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
@endpush
