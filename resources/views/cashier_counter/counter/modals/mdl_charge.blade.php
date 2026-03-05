<div class="modal fade" id="mdl_charge" tabindex="-1" aria-labelledby="mdl_charge_label" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <i class="fa fa-cogs text-primary me-2"></i>
                <div>
                    <h5 class="modal-title mb-0" id="mdl_charge_label">Cobrar Pedido</h5>
                    <span class="text-muted small">Solo se permite vuelto de efectivo</span>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                @include('cashier_counter.counter.forms.form_charge')
            </div>

        </div>
    </div>
</div>
