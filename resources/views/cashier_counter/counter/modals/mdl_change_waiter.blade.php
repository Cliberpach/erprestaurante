<div class="modal fade" id="mdlChangeWaiter" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content shadow">

            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="fas fa-user-edit"></i>
                    <span class="fw-semibold">Cambiar Mesero</span>
                </h5>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="cw-order-id">
                <div class="mb-1">
                    <label class="form-label fw-bold">Mesero:</label>
                    <select id="cw-waiter-select" class="form-select">
                        <option value="">Seleccione un mesero</option>
                    </select>
                    <small class="text-muted">Solo meseros de tu caja activa</small>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-confirm-change-waiter">
                    <i class="fas fa-check me-1"></i> Confirmar
                </button>
            </div>

        </div>
    </div>
</div>

<script>
    let cwWaiterTs = null;

    function eventsMdlChangeWaiter() {
        cwWaiterTs = new TomSelect('#cw-waiter-select', {
            valueField: 'value',
            labelField: 'text',
            searchField: ['text'],
            placeholder: 'Seleccione un mesero',
            plugins: ['clear_button'],
            create: false,
            options: [],
        });

        document.getElementById('btn-confirm-change-waiter').addEventListener('click', async () => {
            toastr.clear();
            const orderId  = document.getElementById('cw-order-id').value;
            const waiterId = cwWaiterTs.getValue();

            if (!waiterId) {
                toastr.warning('Seleccione un mesero');
                return;
            }

            try {
                mostrarAnimacion1();
                const res = await axios.put(
                    route('tenant.mostrador_cajero.mostrador.changeWaiter', orderId),
                    { waiter_id: waiterId }
                );

                if (res.data.success) {
                    toastr.success(res.data.message);
                    bootstrap.Modal.getInstance(document.getElementById('mdlChangeWaiter')).hide();
                    dtList.ajax.reload();
                } else {
                    toastr.error(res.data.message);
                }
            } catch (e) {
                toastr.error('Error al cambiar mesero');
            } finally {
                ocultarAnimacion1();
            }
        });
    }

    async function openMdlChangeWaiter(orderId, currentWaiterId) {
        document.getElementById('cw-order-id').value = orderId;

        cwWaiterTs.clear();
        cwWaiterTs.clearOptions();
        cwWaiterTs.addOption({ value: '', text: 'Cargando...' });
        cwWaiterTs.refreshOptions(false);

        const modal = new bootstrap.Modal(document.getElementById('mdlChangeWaiter'));
        modal.show();

        try {
            const res = await axios.get(route('tenant.mostrador_cajero.mostrador.getWaiters'));

            cwWaiterTs.clear();
            cwWaiterTs.clearOptions();

            if (res.data.success) {
                const waiters = res.data.data.filter(w => w.id != currentWaiterId);

                if (waiters.length === 0) {
                    cwWaiterTs.addOption({ value: '', text: 'No hay otros meseros en tu caja activa' });
                    cwWaiterTs.refreshOptions(false);
                    return;
                }

                waiters.forEach(w => cwWaiterTs.addOption({ value: w.id, text: w.name }));
                cwWaiterTs.refreshOptions(false);
            } else {
                toastr.error(res.data.message);
            }
        } catch (e) {
            toastr.error('Error al obtener meseros');
        }
    }
</script>
