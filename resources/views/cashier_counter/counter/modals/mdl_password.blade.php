<div class="modal fade" id="mdlPassword" tabindex="-1" aria-labelledby="mdlDeleteOrderLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow">

            <!-- HEADER -->
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2" id="mdlDeleteOrderLabel">
                    <i class="fas fa-key"></i>

                    <span id="spanMdlPassTitle" class="fw-semibold d-flex flex-column">
                        <span id="mdlTitleMain">Título</span>
                        <small id="mdlTitleSub" class="text-muted" style="font-size:12px;"></small>
                    </span>
                </h5>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body">
                @include('cashier_counter.counter.forms.form_password')
            </div>

            <div class="modal-footer">
                <button class="btn btn-danger" type="submit" form="form-password" id="btn-confirm-action">
                    <i class="fas fa-lock"></i> Confirmar
                </button>
            </div>

        </div>
    </div>
</div>
