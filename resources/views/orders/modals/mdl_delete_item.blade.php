<div class="modal fade" id="mdlDeleteItem" tabindex="-1" aria-labelledby="mdlDeleteItemLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow">

            <!-- HEADER -->
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2" id="mdlDeleteItemLabel">
                    <i class="fas fa-trash-can"></i>
                    Eliminar item
                </h5>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body">
                @include('orders.forms.form_delete_item')
            </div>

            <div class="modal-footer">
                <button class="btn btn-danger" type="submit" form="form-delete-item" id="btn-change-tbl">
                    <i class="fas fa-save"></i> Eliminar
                </button>
            </div>

        </div>
    </div>
</div>

@push('js-script')
    <script>
        const paramsMdlDeleteItem = {
            itemIndex: null
        }

        function eventsMdlDeleteItem() {
            document.querySelector('#form-delete-item').addEventListener('submit', (e) => {
                e.preventDefault();
                deleteItem(e.target);
            })

            const modal = document.getElementById('mdlDeleteItem');
            modal.addEventListener('hidden.bs.modal', function() {
                clearFormDeleteItem();
            });
            modal.addEventListener('shown.bs.modal', function() {
                document.querySelector('#password').focus();
            });
        }

        async function openMdlDeleteItem(itemIndex) {
            paramsMdlDeleteItem.itemIndex = itemIndex;
            //document.querySelector('#spanItemDelete').textContent = paramsMdlShow.order.order_code;
            $('#mdlDeleteItem').modal('show');
        }

        function actionBtnEditOrder(e) {
            const url = route('tenant.mostrador_mesero.mostrador.edit', {
                id: paramsMdlDeleteItem.orderId
            });
            window.location.href = url;
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
            paramsMdlDeleteItem.tableSelected.id = tableId;
            paramsMdlDeleteItem.tableSelected.name = tableName;

            table.querySelectorAll('.row-tbl-selected').forEach(r => r.classList.remove('row-tbl-selected'));
            row.classList.add('row-tbl-selected');
        }

        function deleteItem(formDeleteItem) {
            toastr.clear();
            Swal.fire({
                title: `Eliminar item?`,
                text: "Se eliminará el item del detalle!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí!",
                cancelButtonText: "No, cancelar!",
                reverseButtons: true
            }).then(async (result) => {
                if (result.isConfirmed) {

                    Swal.fire({
                        title: `Verificando contraseña`,
                        html: `Cargando...`,
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    try {

                        const formData = new FormData(formDeleteItem);
                        let url = route('tenant.utils.validationPassword');

                        const res = await axios.post(url, formData);

                        if (res.data.success) {
                            flowDeleteItem(paramsMdlDeleteItem.itemIndex);
                            toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                            $('#mdlDeleteItem').modal('hide');
                        } else {
                            toastr.error(res.data.message, 'ERROR EN EL SERVIDOR VERIFICAR CONTRASEÑA');
                        }

                    } catch (error) {
                        toastr.error(error, 'ERROR EN LA PETICIÓN VERIFICAR CONTRASEÑA');
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

        function clearFormDeleteItem() {
            document.querySelector('#password').value = '';
        }
    </script>
@endpush
