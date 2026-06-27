<div class="modal fade" id="mdlShowOrder" tabindex="-1" aria-labelledby="mdlShowOrderLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow">

            <!-- HEADER -->
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2" id="mdlShowOrderLabel">
                    <i class="fas fa-chair"></i>
                    Mesa: <span id="spanMesaNombre" class="fw-semibold"></span>
                </h5>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body">

                <div class="action-grid">

                    <!-- EDITAR -->
                    <button type="button" id="btnEditOrder" class="action-btn">
                        <div class="action-icon" style="background:#dbeafe;">
                            <i class="fas fa-edit" style="color:#1d4ed8;"></i>
                        </div>
                        <span style="color:#1d4ed8;">Editar</span>
                    </button>

                    <!-- PRECUENTA -->
                    <button type="button" id="btnPreCuenta" class="action-btn">
                        <div class="action-icon" style="background:#fef9c3;">
                            <i class="fas fa-receipt" style="color:#b45309;"></i>
                        </div>
                        <span style="color:#b45309;">Precuenta</span>
                    </button>

                    <!-- CAMBIAR MESA -->
                    <button type="button" id="btnChangeTable" class="action-btn">
                        <div class="action-icon" style="background:#cffafe;">
                            <i class="fas fa-chair" style="color:#0e7490;"></i>
                        </div>
                        <span style="color:#0e7490;">Cambio Mesa</span>
                    </button>

                    <!-- ELIMINAR -->
                    <button type="button" id="btnDeleteOrder" class="action-btn">
                        <div class="action-icon" style="background:#fee2e2;">
                            <i class="fas fa-trash-alt" style="color:#dc2626;"></i>
                        </div>
                        <span style="color:#dc2626;">Eliminar</span>
                    </button>

                    <!-- COBRAR -->
                    <button type="button" id="btnChargeOrder" class="action-btn">
                        <div class="action-icon" style="background:#dcfce7;">
                            <i class="fas fa-cash-register" style="color:#16a34a;"></i>
                        </div>
                        <span style="color:#16a34a;">Cobrar</span>
                    </button>

                    <!-- UNIR MESAS -->
                    <button type="button" id="btnMergeTables" class="action-btn">
                        <div class="action-icon" style="background:#ede9fe;">
                            <i class="fas fa-object-group" style="color:#7c3aed;"></i>
                        </div>
                        <span style="color:#7c3aed;">Unir Mesas</span>
                    </button>

                </div>

                <style>
                    .action-grid {
                        display: grid;
                        grid-template-columns: repeat(3, 1fr);
                        gap: 10px;
                        margin-bottom: 16px;
                    }

                    .action-btn {
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        gap: 6px;
                        padding: 12px 6px;
                        border: none;
                        background: #f8f9fa;
                        border-radius: 14px;
                        cursor: pointer;
                        transition: transform .15s ease, box-shadow .15s ease;
                        font-size: .75rem;
                        font-weight: 700;
                        line-height: 1.2;
                        text-align: center;
                    }

                    .action-btn:hover {
                        transform: translateY(-2px);
                        box-shadow: 0 4px 12px rgba(0,0,0,.1);
                    }

                    .action-btn:active {
                        transform: scale(.96);
                    }

                    .action-icon {
                        width: 48px;
                        height: 48px;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 1.25rem;
                    }

                    @media (max-width: 400px) {
                        .action-icon {
                            width: 40px;
                            height: 40px;
                            font-size: 1.05rem;
                        }
                        .action-btn {
                            font-size: .68rem;
                            padding: 10px 4px;
                        }
                    }
                </style>


                <!-- CARD COLAPSABLE -->
                <div class="card mb-2 shadow-sm">

                    <!-- HEADER CLICKABLE -->
                    <div class="card-header bg-white p-2" role="button" data-bs-toggle="collapse"
                        data-bs-target="#detailsSection" aria-expanded="false" aria-controls="detailsSection">

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-semibold text-primary">
                                <i class="fas fa-info-circle me-1"></i> Detalles del pedido
                            </span>
                            <i class="fas fa-chevron-down"></i>
                        </div>

                    </div>

                    <!-- BODY -->
                    <div id="detailsSection" class="collapse">
                        <div class="card-body">

                            <!-- INFO GENERAL -->
                            <div class="row g-3 mb-3">

                                <div class="col-12">
                                    <div class="card mb-3 shadow-sm">
                                        <div class="card-body py-3">
                                            <div class="row align-items-center text-center">

                                                <!-- CLIENTE -->
                                                <div class="col-6 border-end">
                                                    <i class="fas fa-user text-primary fs-4 mb-1"></i>
                                                    <div class="small text-muted">Cliente</div>
                                                    <div class="fw-bold text-truncate" id="customerName"></div>
                                                </div>

                                                <!-- TOTAL -->
                                                <div class="col-6">
                                                    <i class="fas fa-coins text-success fs-4 mb-1"></i>
                                                    <div class="small text-muted">Total</div>
                                                    <div class="fw-bold fs-5 text-success" id="total"></div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- DATOS DEL PEDIDO -->
                                <div class="col-12 col-md-6">
                                    <div class="bg-light h-100 rounded border p-3">

                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-receipt text-primary me-2"></i>
                                            <span class="fw-semibold">Datos del pedido</span>
                                        </div>

                                        <p class="d-flex align-items-center mb-2">
                                            <i class="fas fa-hashtag text-muted me-2"></i>
                                            <span class="text-muted me-1">ID:</span>
                                            <span id="noteId" class="fw-semibold"></span>
                                        </p>

                                        <p class="d-flex align-items-center mb-2">
                                            <i class="fas fa-file-invoice text-muted me-2"></i>
                                            <span class="text-muted me-1">N° Pedido:</span>
                                            <span id="orderNumber" class="fw-semibold"></span>
                                        </p>

                                        <p class="d-flex align-items-center mb-0">
                                            <i class="fas fa-id-card text-muted me-2"></i>
                                            <span class="text-muted me-1">Documento:</span>
                                            <span id="customerDocument" class="fw-semibold"></span>
                                        </p>

                                    </div>
                                </div>

                                <!-- DATOS DE ATENCIÓN -->
                                <div class="col-12 col-md-6">
                                    <div class="bg-light h-100 rounded border p-3">

                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-user-tie text-success me-2"></i>
                                            <span class="fw-semibold">Atención</span>
                                        </div>

                                        <p class="d-flex align-items-center mb-2">
                                            <i class="fas fa-user text-muted me-2"></i>
                                            <span class="text-muted me-1">Mesero:</span>
                                            <span id="waiterName" class="fw-semibold"></span>
                                        </p>

                                        <p class="d-flex align-items-center mb-2">
                                            <i class="fas fa-calendar-alt text-muted me-2"></i>
                                            <span class="text-muted me-1">Fecha:</span>
                                            <span id="createdAt" class="fw-semibold"></span>
                                        </p>

                                        <p class="d-flex align-items-center mb-0">
                                            <i class="fas fa-info-circle text-muted me-2"></i>
                                            <span class="text-muted me-1">Estado:</span>
                                            <span id="estado" class="badge bg-secondary"></span>
                                        </p>

                                    </div>
                                </div>

                            </div>

                            <!-- MONTOS -->
                            <div class="row g-2 mb-3 text-center">

                                <div class="col-12">

                                </div>

                            </div>

                            <!-- OBSERVACIÓN -->
                            <div class="bg-light rounded border p-3">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas fa-comment-dots text-secondary me-2"></i>
                                    <strong>Observación</strong>
                                </div>
                                <p class="text-muted mb-0" id="observation"></p>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

@push('js-script')
    <script>
        let dtDetailShow = null;

        const paramsMdlShow = {
            orderId: null,
            tableId: null,
            order: null
        }

        function eventsMdlOrderShow() {
            document.querySelector('#btnEditOrder').addEventListener('click', (e) => {
                actionBtnEditOrder(e);
            })

            document.querySelector('#btnPreCuenta').addEventListener('click', (e) => {
                printPreAccount(paramsMdlShow.orderId);
            })

            document.querySelector('#btnChangeTable').addEventListener('click', (e) => {
                openMdlChangeTbl(paramsMdlShow.orderId);
            })

            document.querySelector('#btnDeleteOrder').addEventListener('click', (e) => {
                openMdlDeleteOrder(paramsMdlShow.orderId);
            })

            document.querySelector('#btnChargeOrder').addEventListener('click', (e) => {
                openOfFCharge(paramsMdlShow.order);
            })

            document.querySelector('#btnMergeTables').addEventListener('click', () => {
                openMdlMergeTables(
                    paramsMdlShow.orderId,
                    paramsMdlShow.tableId,
                    paramsMdlShow.order.table_name
                );
            })
        }

        async function openMdlShowOrder(tableId, orderId) {
            paramsMdlShow.orderId = orderId;
            paramsMdlShow.tableId = tableId;
            const data = await getOrderTable(tableId);
            paramsMdlShow.order = data;
            paintOrderTable(data);
            $('#mdlShowOrder').modal('show');
        }

        function actionBtnEditOrder(e) {
            const url = route('tenant.mostrador_mesero.mostrador.edit', {
                id: paramsMdlShow.orderId
            });
            window.location.href = url;
        }

        async function getOrderTable(tableId) {
            try {
                toastr.clear();
                mostrarAnimacion1();

                const url = route('tenant.mostrador_mesero.mostrador.getOrderTable', {
                    table: tableId
                });

                const res = await axios.get(url);

                if (res.data.success) {
                    toastr.success('ORDEN MESA OBTENIDA');
                    return res.data.data;
                } else {
                    toastr.error(res.message, 'ERROR EN EL SERVIDOR');
                    return null;
                }

            } catch (error) {
                toastr.error(error, 'ERROR EN LA PETICIÓN OBTENER ORDEN DE MESA');
                return null;
            } finally {
                ocultarAnimacion1();
            }
        }

        function paintOrderTable(data) {
            if (!data) return;

            // HEADER
            document.querySelector('#spanMesaNombre').textContent = data.table_name ?? '-';

            // INFO GENERAL
            document.querySelector('#noteId').textContent = data.order_id ?? '-';
            document.querySelector('#orderNumber').textContent = data.order_code ?? '-';
            document.querySelector('#customerName').textContent = data.customer_name ?? '-';

            const documentText = (data.customer_type_document_abbreviation && data.customer_document_number) ?
                `${data.customer_type_document_abbreviation}-${data.customer_document_number}` :
                '-';
            document.querySelector('#customerDocument').textContent = documentText;

            // MESERO / USUARIO
            document.querySelector('#waiterName').textContent = data.creator_user_name ?? '-';

            // FECHA
            document.querySelector('#createdAt').textContent = data.created_at ?? '-';

            // ESTADO (badge con color)
            const estadoEl = document.querySelector('#estado');
            estadoEl.textContent = data.status ?? '-';

            estadoEl.classList.remove('bg-secondary', 'bg-success', 'bg-danger', 'bg-primary');
            switch (data.status) {
                case 'ACTIVO':
                    estadoEl.classList.add('bg-primary');
                    break;
                case 'FINALIZADO':
                    estadoEl.classList.add('bg-success');
                    break;
                case 'ANULADO':
                    estadoEl.classList.add('bg-danger');
                    break;
                default:
                    estadoEl.classList.add('bg-secondary');
            }

            // MONTOS
            document.querySelector('#total').textContent = `S/ ${formatSoles(data.total)}`;

            // OBSERVACIÓN
            document.querySelector('#observation').textContent = data.observation ?? '—';
        }


        function paintNoteIncomeDetail(details) {

            const tbody = document.querySelector("#tbl_note_income_show tbody");

            details.forEach(detail => {
                const row = document.createElement("tr");

                row.innerHTML = `
                <td>${detail.product_name}</td>
                <td>${detail.category_name}</td>
                <td>${detail.brand_name}</td>
                <td>${detail.quantity}</td>
            `;

                tbody.appendChild(row);
            });
        }

        function printPreAccount(orderId) {
            toastr.clear();

            let message = `Imprimir Pre-Cuenta PE-${orderId}`;
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
                        title: 'Cargando...',
                        html: 'Impriendo precuenta...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    try {

                        const formData = new FormData();
                        formData.append('order_id', orderId);
                        let url = route('tenant.mostrador_mesero.mostrador.preCuenta', formData);

                        const res = await axios.post(url, formData);

                        if (res.data.success) {
                            //dtOrders.ajax.reload();
                            toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                        } else {
                            toastr.error(res.data.message, 'ERROR EN EL SERVIDOR AL IMPRIMIR PRECUENTA');
                        }

                    } catch (error) {
                        toastr.error(error, 'ERROR EN LA PETICIÓN IMPRIMIR PRECUENTA');
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
    </script>
@endpush
