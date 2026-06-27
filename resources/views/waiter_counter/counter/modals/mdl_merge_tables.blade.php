<div class="modal fade" id="mdlMergeTables" tabindex="-1" aria-labelledby="mdlMergeTablesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow">

            <!-- HEADER -->
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2" id="mdlMergeTablesLabel">
                    <i class="fas fa-object-group" style="color:#7c3aed;"></i>
                    Unir Mesas — <span id="spanMasterTableName" class="fw-semibold"></span>
                </h5>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body">

                <div class="alert alert-info d-flex align-items-center gap-2 py-2 mb-3" role="alert">
                    <i class="fas fa-info-circle"></i>
                    <span>
                        Máximo permitido: <strong id="spanMaxTables">4</strong> mesas en total (incluida la actual).
                        Puedes tener hasta <strong id="spanMaxSlaves">3</strong> mesas adicionales.
                    </span>
                </div>

                <!-- MESAS YA UNIDAS -->
                <div id="fused-section" class="d-none mb-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-warning text-dark">Ya unidas</span>
                        <span class="text-muted small">Presiona ✕ para desvincular</span>
                    </div>
                    <div id="fused-tables-list"></div>
                </div>

                <!-- MESAS LIBRES -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Mesas libres disponibles</span>
                    <span class="badge bg-secondary" id="spanSelectedCount">0 seleccionadas</span>
                </div>

                <div id="merge-tables-list"></div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btn-confirm-merge">
                    <i class="fas fa-object-group"></i> UNIR MESAS
                </button>
            </div>

        </div>
    </div>
</div>

<style>
    .merge-table-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.18s ease;
        margin-bottom: 8px;
        background: #fff;
        user-select: none;
    }

    .merge-table-item:hover:not(.merge-table-disabled) {
        border-color: #7c3aed;
        background: #f5f3ff;
    }

    .merge-table-item.merge-table-selected {
        border-color: #7c3aed;
        background: linear-gradient(90deg, #ede9fe, #f5f3ff);
        box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.2);
    }

    .merge-table-item.merge-table-disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    .merge-table-check {
        width: 22px;
        height: 22px;
        border: 2px solid #ced4da;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.15s ease;
    }

    .merge-table-selected .merge-table-check {
        background: #7c3aed;
        border-color: #7c3aed;
        color: #fff;
    }

    .merge-table-name {
        font-weight: 700;
        font-size: 1rem;
        color: #212529;
        flex: 1;
    }

    /* mesas ya unidas */
    .fused-table-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 14px;
        border: 2px solid #ffc107;
        border-radius: 10px;
        background: #fffbeb;
        margin-bottom: 8px;
    }

    .fused-table-name {
        font-weight: 700;
        font-size: .95rem;
        color: #92400e;
        flex: 1;
    }

    .btn-unmerge {
        border: none;
        background: #fee2e2;
        color: #dc2626;
        border-radius: 6px;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.15s;
        flex-shrink: 0;
    }

    .btn-unmerge:hover {
        background: #fca5a5;
    }
</style>

@push('js-script')
    <script>
        const paramsMdlMerge = {
            orderId: null,
            masterTableId: null,
            masterTableName: null,
            maxTables: 4,
            selectedSlaves: [],
            currentFusions: []   // [{fusion_id, slave_table_id, table_name}]
        };

        function eventsMdlMergeTables() {
            document.querySelector('#btn-confirm-merge').addEventListener('click', () => {
                actionConfirmMerge();
            });
        }

        async function openMdlMergeTables(orderId, masterTableId, masterTableName) {
            toastr.clear();
            paramsMdlMerge.orderId         = orderId;
            paramsMdlMerge.masterTableId   = masterTableId;
            paramsMdlMerge.masterTableName = masterTableName;
            paramsMdlMerge.selectedSlaves  = [];
            paramsMdlMerge.currentFusions  = [];

            document.querySelector('#spanMasterTableName').textContent    = masterTableName;
            document.querySelector('#merge-tables-list').innerHTML        = '';
            document.querySelector('#fused-tables-list').innerHTML        = '';
            document.querySelector('#fused-section').classList.add('d-none');
            document.querySelector('#btn-confirm-merge').disabled         = false;
            updateMergeCounter();

            try {
                mostrarAnimacion1();

                // 1. config + mesas libres (críticos)
                const [configRes, tablesRes] = await Promise.all([
                    axios.get(route('tenant.mostrador_mesero.mostrador.getFusionConfig')),
                    axios.get(route('tenant.mostrador_mesero.mostrador.getTablesFree'))
                ]);

                paramsMdlMerge.maxTables = configRes.data.max_tables ?? 4;
                document.querySelector('#spanMaxTables').textContent = paramsMdlMerge.maxTables;
                document.querySelector('#spanMaxSlaves').textContent = paramsMdlMerge.maxTables - 1;

                // 2. fusiones activas del pedido (carga separada para no bloquear si falla)
                try {
                    const fusionsRes = await axios.get(
                        route('tenant.mostrador_mesero.mostrador.getActiveFusionsByOrder', { order_id: orderId })
                    );
                    paramsMdlMerge.currentFusions = fusionsRes.data.data ?? [];
                } catch (fusionErr) {
                    console.error('No se pudieron cargar fusiones activas:', fusionErr);
                    paramsMdlMerge.currentFusions = [];
                }

                paintFusedTables(paramsMdlMerge.currentFusions);

                const maxSlaves    = paramsMdlMerge.maxTables - 1;
                const alreadyFused = paramsMdlMerge.currentFusions.length;
                const slotsLeft    = maxSlaves - alreadyFused;

                if (slotsLeft <= 0) {
                    document.querySelector('#merge-tables-list').innerHTML = `
                        <div class="alert alert-warning d-flex align-items-center gap-2 py-2">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Máximo de mesas alcanzado (<strong>${paramsMdlMerge.maxTables}</strong>).
                            Desvincular alguna mesa para poder agregar otra.</span>
                        </div>`;
                    document.querySelector('#btn-confirm-merge').disabled = true;
                    document.querySelector('#spanSelectedCount').textContent = '0 seleccionadas';
                } else {
                    document.querySelector('#btn-confirm-merge').disabled = false;
                    paintMergeTables(tablesRes.data.data ?? []);
                }

            } catch (e) {
                toastr.clear();
                toastr.error('Error al cargar datos de mesas');
                console.error(e);
            } finally {
                ocultarAnimacion1();
            }

            $('#mdlMergeTables').modal('show');
        }

        function paintFusedTables(fusions) {
            const section   = document.querySelector('#fused-section');
            const container = document.querySelector('#fused-tables-list');
            container.innerHTML = '';

            if (!fusions || fusions.length === 0) {
                section.classList.add('d-none');
                return;
            }

            section.classList.remove('d-none');

            fusions.forEach(f => {
                const item = document.createElement('div');
                item.className = 'fused-table-item';
                item.innerHTML = `
                    <i class="fas fa-link text-warning"></i>
                    <div class="fused-table-name">
                        <i class="fas fa-chair me-1"></i> ${f.table_name}
                    </div>
                    <button class="btn-unmerge" title="Desvincular mesa" data-fusion-id="${f.fusion_id}" data-table-name="${f.table_name}">
                        <i class="fas fa-times" style="font-size:.75rem;"></i>
                    </button>`;

                item.querySelector('.btn-unmerge').addEventListener('click', (e) => {
                    e.stopPropagation();
                    actionUnmerge(f.fusion_id, f.table_name);
                });

                container.appendChild(item);
            });
        }

        function paintMergeTables(tables) {
            const container = document.querySelector('#merge-tables-list');
            container.innerHTML = '';

            // excluir master y mesas ya unidas
            const fusedIds  = paramsMdlMerge.currentFusions.map(f => parseInt(f.slave_table_id));
            const available = tables.filter(t =>
                parseInt(t.id) !== parseInt(paramsMdlMerge.masterTableId) &&
                !fusedIds.includes(parseInt(t.id))
            );

            if (available.length === 0) {
                container.innerHTML = `
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-chair fs-2 mb-2 d-block"></i>
                        No hay mesas libres disponibles para agregar.
                    </div>`;
                return;
            }

            available.forEach(table => {
                const item = document.createElement('div');
                item.className    = 'merge-table-item';
                item.dataset.id   = table.id;
                item.dataset.name = table.name;
                item.innerHTML = `
                    <div class="merge-table-check">
                        <i class="fas fa-check" style="font-size:.75rem;"></i>
                    </div>
                    <div class="merge-table-name">
                        <i class="fas fa-chair me-1 text-muted"></i> ${table.name}
                    </div>`;

                item.addEventListener('click', () => toggleMergeTable(item));
                container.appendChild(item);
            });

            refreshMergeDisabledState();
        }

        function toggleMergeTable(item) {
            const id         = parseInt(item.dataset.id);
            const maxSlaves  = paramsMdlMerge.maxTables - 1;
            const isSelected = item.classList.contains('merge-table-selected');
            const alreadyFused = paramsMdlMerge.currentFusions.length;

            if (!isSelected && (paramsMdlMerge.selectedSlaves.length + alreadyFused) >= maxSlaves) {
                toastr.clear();
                toastr.warning(`Máximo ${maxSlaves} mesas adicionales permitidas (ya hay ${alreadyFused} unidas).`);
                return;
            }

            if (isSelected) {
                item.classList.remove('merge-table-selected');
                paramsMdlMerge.selectedSlaves = paramsMdlMerge.selectedSlaves.filter(s => s !== id);
            } else {
                item.classList.add('merge-table-selected');
                paramsMdlMerge.selectedSlaves.push(id);
            }

            updateMergeCounter();
            refreshMergeDisabledState();
        }

        function updateMergeCounter() {
            const count = paramsMdlMerge.selectedSlaves.length;
            document.querySelector('#spanSelectedCount').textContent =
                count === 0 ? '0 seleccionadas' : `${count} seleccionada${count > 1 ? 's' : ''}`;
        }

        function refreshMergeDisabledState() {
            const maxSlaves   = paramsMdlMerge.maxTables - 1;
            const alreadyFused = paramsMdlMerge.currentFusions.length;
            const atMax       = (paramsMdlMerge.selectedSlaves.length + alreadyFused) >= maxSlaves;

            document.querySelectorAll('.merge-table-item').forEach(item => {
                const isSelected = item.classList.contains('merge-table-selected');
                if (atMax && !isSelected) {
                    item.classList.add('merge-table-disabled');
                } else {
                    item.classList.remove('merge-table-disabled');
                }
            });
        }

        async function actionUnmerge(fusionId, tableName) {
            Swal.fire({
                title: '¿Desvincular mesa?',
                html: `Mesa <strong>${tableName}</strong> quedará libre.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'SÍ, DESVINCULAR',
                cancelButtonText: 'CANCELAR',
                reverseButtons: true
            }).then(async (result) => {
                if (!result.isConfirmed) return;

                Swal.fire({
                    title: 'Desvinculando...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                try {
                    const res = await axios.delete(
                        route('tenant.mostrador_mesero.mostrador.unmergeTable', { fusion_id: fusionId })
                    );

                    Swal.close();

                    if (res.data.success) {
                        toastr.clear();
                        toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                        loadTablesAsCircles();
                        await openMdlMergeTables(
                            paramsMdlMerge.orderId,
                            paramsMdlMerge.masterTableId,
                            paramsMdlMerge.masterTableName
                        );
                    } else {
                        toastr.clear();
                        toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                    }
                } catch (e) {
                    Swal.close();
                    toastr.clear();
                    toastr.error('Error al desvincular mesa');
                }
            });
        }

        async function actionConfirmMerge() {
            if (paramsMdlMerge.selectedSlaves.length === 0) {
                toastr.clear();
                toastr.warning('Selecciona al menos una mesa a unir.');
                return;
            }

            const names = [...document.querySelectorAll('.merge-table-selected')]
                .map(el => el.dataset.name)
                .join(', ');

            Swal.fire({
                title: '¿UNIR MESAS?',
                html: `Mesa <strong>${paramsMdlMerge.masterTableName}</strong> + <strong>${names}</strong>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'SÍ, UNIR',
                cancelButtonText: 'CANCELAR',
                reverseButtons: true
            }).then(async (result) => {
                if (!result.isConfirmed) return;

                Swal.fire({
                    title: 'Fusionando mesas...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                try {
                    const res = await axios.post(
                        route('tenant.mostrador_mesero.mostrador.mergeTables'),
                        {
                            order_id:        paramsMdlMerge.orderId,
                            master_table_id: paramsMdlMerge.masterTableId,
                            slave_table_ids: paramsMdlMerge.selectedSlaves
                        }
                    );

                    if (res.data.success) {
                        toastr.clear();
                        toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                        $('#mdlMergeTables').modal('hide');
                        $('#mdlShowOrder').modal('hide');
                        loadTablesAsCircles();
                    } else {
                        toastr.clear();
                        toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                    }
                } catch (e) {
                    toastr.clear();
                    toastr.error('Error al fusionar mesas');
                } finally {
                    Swal.close();
                }
            });
        }
    </script>
@endpush
