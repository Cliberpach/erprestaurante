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

        async function getTablesFree() {
            try {
                toastr.clear();
                mostrarAnimacion1();

                const url = route('tenant.mostrador_mesero.mostrador.getTablesFree');

                const res = await axios.get(url);

                if (res.data.success) {
                    toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                    return res.data.data;
                } else {
                    toastr.error(res.message, 'ERROR EN EL SERVIDOR');
                    return null;
                }

            } catch (error) {
                toastr.error(error, 'ERROR EN LA PETICIÓN OBTENER MESAS LIBRES');
                return null;
            } finally {
                ocultarAnimacion1();
            }
        }

        function paintTablesFree(data) {
            if (!data) return;
            const tbody = document.querySelector('#dt-tables-free tbody');
            let rows = ``;
            data.forEach((table) => {
                rows += `
                <tr class="row-tbl-change" data-table="${table.id}" data-table-name="${table.name}">
                    <td>${table.id}</td>
                    <td style="text-align:center;">${table.name}</td>
                </tr>
                `
            })
            tbody.innerHTML = rows;
        }

        function actionTblSelected(row, table) {
            const tableId = row.getAttribute('data-table');
            const tableName = row.getAttribute('data-table-name');
            paramsMdlDeleteOrder.tableSelected.id = tableId;
            paramsMdlDeleteOrder.tableSelected.name = tableName;

            table.querySelectorAll('.row-tbl-selected').forEach(r => r.classList.remove('row-tbl-selected'));
            row.classList.add('row-tbl-selected');
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
