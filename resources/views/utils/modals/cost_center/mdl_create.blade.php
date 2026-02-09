<div class="modal fade" id="mdlCostCenter" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Registrar Centro Costo</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @include('utils.modals.cost_center.forms.form_cost_center')
            </div>
            <div class="modal-footer">

                <div class="col-12">

                    <div class="row">
                        <div class="col-12 d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                style="margin-right: 6px;">Cerrar</button>
                            <button class="btn btn-primary btnstoreCustomer" type="submit" form="form_cost_center">
                                <i class="fa-solid fa-floppy-disk"></i> Registrar
                            </button>
                        </div>

                        <div class="col-12">
                            <p style="display: block;margin:0;padding:0;font-weight:bold;" class="color_warning">Los
                                campos con (*) son obligatorios</p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>


<script>
    const paramsCostCenter = {
        name: null
    }

    function openMdlCostCenter() {
        $('#mdlCostCenter').modal('show');
    }

    function eventsMdlCostCenter() {

        document.querySelector('#form_cost_center').addEventListener('submit', (e) => {
            e.preventDefault();
            storeCostCenter(e.target);
        })

        $('#mdlCostCenter').on('hidden.bs.modal', function() {
            clearMdlCostCenter();
        });
    }

    function storeCostCenter(formstoreCostCenter) {
        Swal.fire({
            title: "Desea registrar el centro de costos?",
            text: "Se creará un nuevo centro de costos!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí!",
            cancelButtonText: "No!",
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {

                clearValidationErrors('msgError');
                const token = document.querySelector('input[name="_token"]').value;
                const formData = new FormData(formstoreCostCenter);
                const urlStore = @json(route('tenant.utils.storeCostCenter'));

                Swal.fire({
                    title: 'Cargando...',
                    html: 'Registrando centro de costos...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    const response = await fetch(urlStore, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        body: formData
                    });

                    const res = await response.json();

                    if (response.status === 422) {
                        if ('errors' in res) {
                            paintValidationErrors(res.errors, 'mdlcostcenter_error');
                        }
                        Swal.close();
                        return;
                    }

                    if (res.success) {
                        toastr.success(res.message, 'OPERACIÓN COMPLETADA');
                        setNewCostCenter(res.item);
                        $('#mdlCostCenter').modal('hide');
                    } else {
                        toastr.error(res.message, 'ERROR EN EL SERVIDOR');
                    }

                } catch (error) {
                    toastr.error(error, 'ERROR EN LA PETICIÓN REGISTRAR CENTRO DE COSTOS');
                } finally {
                    Swal.close();
                }

            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire({
                    title: "OPERACIÓN CANCELADA",
                    text: "NO SE REALIZARON ACCIONES",
                    icon: "error"
                });
            }
        });
    }

    function setNewCostCenter(_item) {
        window.costCenterSelect.clear();

        const item = {
            id: _item.id,
            name: _item.name
        }

        window.costCenterSelect.addOption(item);
        window.costCenterSelect.setValue(item.id);
    }

    function clearMdlCostCenter() {
        document.querySelector('#name_mdlcostcenter').value = '';
    }
</script>
