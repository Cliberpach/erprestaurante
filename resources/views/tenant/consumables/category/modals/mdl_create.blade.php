<div class="modal fade" id="mdlCreateCategory" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Registrar Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                @include('tenant.consumables.category.forms.form_create')

            </div>
            <div class="modal-footer flex-column align-items-stretch">

                <div class="d-flex justify-content-end w-100 gap-2">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cerrar
                    </button>

                    <button form="createCategoryForm" type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Guardar
                    </button>
                </div>

                <div class="d-flex align-items-center text-muted small mt-2">
                    <i class="fas fa-info-circle me-2"></i>
                    <span>Los campos marcados con asterisco (*) son obligatorios.</span>
                </div>

            </div>

        </div>
    </div>
</div>

<script>
    function openMdlCreate() {
        $('#mdlCreateCategory').modal('show');
    }

    function eventsMdlCreate() {

        document.querySelector('#mdlCreateCategory').addEventListener('submit', (e) => {
            e.preventDefault();
            storeCategory(e.target);
        })

        $('#mdlCreateCategory').on('hidden.bs.modal', function(e) {
            const createCategoryForm = document.querySelector('#createCategoryForm');
            createCategoryForm.reset();
            clearValidationErrors('msgError');
        });

    }

    function storeCategory(form) {
        Swal.fire({
            title: "DESEA REGISTRAR LA CATEGORÍA?",
            text: "Se creará una nueva categoría!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "SÍ!",
            cancelButtonText: "NO!",
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {

                Swal.fire({
                    title: 'Cargando...',
                    html: 'Registrando nueva categoría...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {

                    clearValidationErrors('msgError');
                    const token = document.querySelector('input[name="_token"]').value;
                    const formData = new FormData(form);
                    const urlstoreCategory = @json(route('tenant.insumos.categorias.store'));

                    const response = await fetch(urlstoreCategory, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        body: formData
                    });

                    const res = await response.json();

                    console.log(res);

                    if (response.status === 422) {
                        if ('errors' in res) {
                            paintValidationErrors(res.errors, 'error');
                        }
                        Swal.close();
                        return;
                    }

                    if (res.success) {
                        dtCategories.ajax.reload();
                        $('#mdlCreateCategory').modal('hide');
                        toastr.success(res.message, 'OPERACIÓN COMPLETADA');
                        Swal.close();
                    } else {
                        toastr.error(res.message, 'ERROR EN EL SERVIDOR');
                        Swal.close();
                    }


                } catch (error) {
                    toastr.error(error, 'ERROR EN LA PETICIÓN REGISTRAR CATEGORÍA');
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
</script>
