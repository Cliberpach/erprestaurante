<div class="modal fade" id="mdlShowOrder" tabindex="-1" aria-labelledby="mdlShowOrderLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow">

            <!-- HEADER -->
            <div class="modal-header text-white">
                <h5 class="modal-title d-flex align-items-center gap-2" id="mdlShowOrderLabel">
                    <i class="fas fa-chair"></i>
                    Mesa: <span id="spanMesaNombre" class="fw-semibold"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body">

                <!-- INFO GENERAL -->
                <div class="row g-3 mb-3">

                    <div class="col-12 col-md-6">
                        <div class="bg-light h-100 rounded border p-3">
                            <p class="mb-2">
                                <i class="fas fa-hashtag text-primary"></i>
                                <strong>ID:</strong> <span id="noteId"></span>
                            </p>
                            <p class="mb-2">
                                <i class="fas fa-receipt text-primary"></i>
                                <strong>N° Pedido:</strong> <span id="orderNumber"></span>
                            </p>
                            <p class="mb-2">
                                <i class="fas fa-user text-primary"></i>
                                <strong>Cliente:</strong> <span id="customerName"></span>
                            </p>
                            <p class="mb-0">
                                <i class="fas fa-id-card text-primary"></i>
                                <strong>Documento:</strong>
                                <span id="customerDocument"></span>
                                <!-- Ej: DNI: 9283928 -->
                            </p>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="bg-light h-100 rounded border p-3">
                            <p class="mb-2">
                                <i class="fas fa-user-tie text-success"></i>
                                <strong>Mesero:</strong> <span id="waiterName"></span>
                            </p>
                            <p class="mb-2">
                                <i class="fas fa-calendar-plus text-success"></i>
                                <strong>Fecha Registro:</strong> <span id="createdAt"></span>
                            </p>
                            <p class="mb-0">
                                <i class="fas fa-info-circle text-success"></i>
                                <strong>Estado:</strong>
                                <span id="estado" class="badge bg-secondary"></span>
                            </p>
                        </div>
                    </div>

                </div>

                <!-- MONTOS -->
                <div class="row g-2 mb-3">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                        <div class="rounded border p-1 text-center">
                            <i class="fas fa-file-invoice-dollar text-info fs-6"></i>
                            <div class="small fw-semibold">Subtotal</div>
                            <div class="fw-semibold" id="subtotal"></div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                        <div class="rounded border p-1 text-center">
                            <i class="fas fa-percent text-warning fs-6"></i>
                            <div class="small fw-semibold">IGV</div>
                            <div class="fw-semibold" id="igv"></div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                        <div class="bg-light rounded border p-1 text-center">
                            <i class="fas fa-coins text-success fs-6"></i>
                            <div class="small fw-bold">Total</div>
                            <div class="fw-bold" id="total"></div>
                        </div>
                    </div>
                </div>

                <!-- OBSERVACIÓN -->
                <div class="col-12 mb-3">
                    <div class="bg-light rounded border p-3">
                        <p class="mb-1">
                            <i class="fas fa-comment-dots text-secondary"></i>
                            <strong>Observación</strong>
                        </p>
                        <p class="mb-0" id="observation"></p>
                    </div>
                </div>

            </div>

            <div class="modal-footer d-flex justify-content-between flex-wrap gap-2">
                <div>
                    <button type="button" class="btn btn-outline-danger" id="btnDeleteNote">
                        <i class="fas fa-trash-alt"></i> Eliminar
                    </button>
                    <button type="button" class="btn btn-outline-primary" id="btnEditNote">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Salir
                </button>
            </div>

        </div>
    </div>
</div>


@push('js-script')
    <script>
        let dtNoteIncomeShow = null;


        async function openMdlShowOrder(tableId) {
            const data = await getOrderTable(tableId);
            paintOrderTable(data);
            $('#mdlShowOrder').modal('show');
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
                `${data.customer_type_document_abbreviation}: ${data.customer_document_number}` :
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
            document.querySelector('#subtotal').textContent = `S/ ${formatSoles(data.subtotal)}`;
            document.querySelector('#igv').textContent = `S/ ${formatSoles(data.igv)}`;
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
    </script>
@endpush
