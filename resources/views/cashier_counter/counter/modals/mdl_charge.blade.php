<div class="modal fade" id="mdl_charge" tabindex="-1" aria-labelledby="mdl_charge_label" aria-hidden="true">
    <div class="modal-lg modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <i class="fa fa-cogs text-primary me-2"></i>
                <div>
                    <h5 class="modal-title mb-0" id="mdl_charge_label">Cobrar Pedido</h5>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                @include('cashier_counter.counter.forms.form_charge')
            </div>

            {{-- <div class="modal-footer d-flex justify-content-between align-items-center flex-wrap">
                <div class="text-warning small">
                    <i class="fa fa-exclamation-circle"></i>
                    Los campos marcados con asterisco (<label class="required"></label>) son obligatorios.
                </div>
                <div class="mt-sm-0 mt-2 text-end">
                    <button type="submit" form="form_create_color" class="btn btn-primary btn-sm">
                        <i class="fa fa-save"></i> Guardar
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">
                        <i class="fa fa-times"></i> Cancelar
                    </button>
                </div>
            </div> --}}

        </div>
    </div>
</div>

<style>
    .swal2-container {
        z-index: 9999999;
    }
</style>
