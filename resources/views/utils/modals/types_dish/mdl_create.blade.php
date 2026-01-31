<div class="modal fade" id="mdlCreateTypeDish" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Registrar Tipo Plato</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @include('utils.modals.types_dish.forms.form_create')
            </div>
            <div class="modal-footer">

                <div class="col-12">

                    <div class="row">
                        <div class="col-12 d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                style="margin-right: 6px;">Cerrar</button>
                            <button class="btn btn-primary btnstoreCustomer" type="submit"
                                form="form_create_type_dish">
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
    const paramsMdlTypeDish = {
        name: null
    }

    function openMdlTypeDish() {
        document.querySelector('#name_mdltypedish').value = paramsMdlTypeDish.name;
        $('#mdlCreateTypeDish').modal('show');
    }

    function eventsMdlTypeDish() {
        document.querySelector('#form_create_type_dish').addEventListener('submit', (e) => {
            e.preventDefault();
            storeTypeDish(e.target);
        })

        $('#mdlCreateTypeDish').on('hidden.bs.modal', function() {
            clearMdlTypeDish();
        });
    }

    function storeTypeDish(formStore) {
        Swal.fire({
            title: "DESEA REGISTRAR EL TIPO DE PLATO?",
            text: "Operación no reversible!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "SÍ!",
            cancelButtonText: "NO, CANCELAR!",
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {

                clearValidationErrors('msgError');
                const token = document.querySelector('input[name="_token"]').value;
                const formData = new FormData(formStore);
                const urlStore = @json(route('tenant.abastecimiento.tipos_plato.store'));

                Swal.fire({
                    title: 'Cargando...',
                    html: 'Registrando nuevo tipo de plato...',
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
                            paintValidationErrors(res.errors, 'mdltypedish_error');
                        }
                        Swal.close();
                        return;
                    }

                    if (res.success) {
                        toastr.success(res.message, 'OPERACIÓN COMPLETADA');
                        setNewTypeDish(res.item);
                        $('#mdlCreateTypeDish').modal('hide');
                    } else {
                        toastr.error(res.message, 'ERROR EN EL SERVIDOR');
                    }

                } catch (error) {
                    toastr.error(error, 'ERROR EN LA PETICIÓN REGISTRAR TIPO DE PLATO');
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

    function setNewTypeDish(instance) {
        window.typeDishSelect.clear();

        const item = {
            id: instance.id,
            name: instance.name,
        }

        window.typeDishSelect.addOption(item);
        window.typeDishSelect.setValue(item.id);
    }

    function clearMdlTypeDish() {
        document.querySelector('#name_mdltypedish').value = '';
    }
</script>
