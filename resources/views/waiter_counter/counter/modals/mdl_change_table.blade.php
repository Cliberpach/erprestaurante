<div class="modal fade" id="mdlChangeTable" tabindex="-1" aria-labelledby="mdlChangeTableLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow">

            <!-- HEADER -->
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2" id="mdlChangeTableLabel">
                    <i class="fas fa-chair"></i>
                    Mesa: <span id="spanMesaNombre" class="fw-semibold"></span>
                </h5>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body">
                @include('waiter_counter.counter.tables.tbl_tables_free')
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary" id="btn-change-tbl">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>

        </div>
    </div>
</div>

<style>
    #dt-tables-free .dt-column-header {
        justify-content: center;
    }

    .row-tbl-change {
        background: #fff;
        transition:
            background-color 0.25s ease,
            box-shadow 0.25s ease,
            transform 0.25s ease;
    }

    .row-tbl-change:hover {
        background-color: #fffbeb;
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.12);
        outline: 2px solid #facc15;
        outline-offset: -2px;
    }

    .row-tbl-change:hover td {
        cursor: pointer;
    }

    /* Fila seleccionada */
    .row-tbl-selected {
        background: linear-gradient(90deg,
                #bbdefb 0%,
                #e3f2fd 60%,
                #ffffff 100%) !important;

        border-left: 6px solid #0d47a1;
        box-shadow:
            inset 0 0 0 2px rgba(13, 71, 161, 0.35),
            inset 0 0 12px rgba(33, 150, 243, 0.35);

        transform: scale(1.005);
        transition: background 0.12s ease, box-shadow 0.12s ease, transform 0.12s ease;
    }

    /* Texto destacado */
    .row-tbl-selected td {
        font-weight: 800;
        color: #0d47a1;
        letter-spacing: 0.5px;
    }
</style>

@push('js-script')
    <script>
        let dtChangeTable = null;

        const paramsMdlChangeTbl = {
            orderId: null,
            tableSelected: {
                id: null,
                name: null
            }
        }

        function eventsMdlChangeTbl() {

            const table = document.querySelector('#dt-tables-free');
            table.addEventListener('click', (e) => {
                const row = e.target.closest('.row-tbl-change');
                if (row) {
                    actionTblSelected(row, table);
                }
            })

            document.querySelector('#btn-change-tbl').addEventListener('click', () => {
                actionChangeTbl();
            })
        }

        async function openMdlChangeTbl(orderId) {
            paramsMdlChangeTbl.orderId = orderId;
            const data = await getTablesFree();

            destroyDataTable(dtChangeTable);
            clearTable('dt-tables-free')
            paintTablesFree(data);
            dtChangeTable = loadDataTableSimple('dt-tables-free', {
                paging: false,
                columnDefs: [{
                    targets: [0],
                    visible: false
                }]
            });


            $('#mdlChangeTable').modal('show');
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
            paramsMdlChangeTbl.tableSelected.id = tableId;
            paramsMdlChangeTbl.tableSelected.name = tableName;

            table.querySelectorAll('.row-tbl-selected').forEach(r => r.classList.remove('row-tbl-selected'));
            row.classList.add('row-tbl-selected');
        }

        function actionChangeTbl() {
            toastr.clear();

            let message = `Cambiar mesa: ${paramsMdlShow.order.table_name} a ${paramsMdlChangeTbl.tableSelected.name}?`;
            Swal.fire({
                title: message,
                text: "Operación no reversible!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí!",
                cancelButtonText: "No, cancelar!",
                reverseButtons: true
            }).then(async (result) => {
                if (result.isConfirmed) {

                    Swal.fire({
                        title: `Cambiando a mesa ${paramsMdlChangeTbl.tableSelected.name}`,
                        html: `Cargando...`,
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    try {

                        const formData = new FormData();
                        formData.append('order_id', paramsMdlChangeTbl.orderId);
                        formData.append('table_selected', paramsMdlChangeTbl.tableSelected.id);
                        let url = route('tenant.mostrador_mesero.mostrador.changeTable', formData);

                        const res = await axios.post(url, formData);

                        if (res.data.success) {
                            loadTablesAsCircles();
                            toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                            $('#mdlChangeTable').modal('hide');
                            $('#mdlShowOrder').modal('hide');
                        } else {
                            toastr.error(res.data.message, 'ERROR EN EL SERVIDOR CAMBIAR MESA');
                        }

                    } catch (error) {
                        toastr.error(error, 'ERROR EN LA PETICIÓN CAMBIAR MESA');
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
