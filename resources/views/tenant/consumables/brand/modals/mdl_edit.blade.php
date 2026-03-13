<!-- Modal -->
<div class="modal fade" id="mdlBrandEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Editar Marca</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @include('tenant.consumables.brand.forms.form_edit')
            </div>
            <div class="modal-footer flex-column align-items-stretch">

                <!-- BOTONES -->
                <div class="d-flex justify-content-end w-100 gap-2">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cerrar
                    </button>

                    <button form="formEditBrand" type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Actualizar
                    </button>
                </div>

                <!-- MENSAJE -->
                <div class="d-flex align-items-center text-muted small mt-2">
                    <i class="fas fa-info-circle me-2"></i>
                    <span>Los campos marcados con asterisco (*) son obligatorios.</span>
                </div>

            </div>

        </div>
    </div>
</div>

<script>
    const parameters = {
        id: null,
        row: null
    };

    function openMdlEdit(id) {
        if (!id) {
            toastr.error('FALTA EL PARÁMETRO ID CATEGORY');
            return;
        }
        const row = getRowById(dtCategories, id);
        if (!row) {
            toastr.error('CATEGORÍA NO ENCONTRADA');
            return;
        }
        parameters.id = id;
        parameters.row = row;
        document.querySelector('#name_edit').value = row.name;
        $('#mdlBrandEdit').modal('show');
    }

    function eventsMdlEdit() {
        document.querySelector('#mdlBrandEdit').addEventListener('submit', (e) => {
            e.preventDefault();
            updateBrand(e.target);
        })

        $('#formEditBrand').on('hidden.bs.modal', function(e) {
            const formEditBrand = document.querySelector('#formEditBrand');
            formEditBrand.reset();
            clearValidationErrors('msgError');
            parameters.id = null;
            parameters.row = null;
        });
    }

    function updateBrand(form) {

        Swal.fire({
            title: "Desea actualizar la marca?",
            text: `Categoría: ${parameters.row.name}`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "SÍ!",
            cancelButtonText: "NO!",
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {


                Swal.fire({
                    title: 'Cargando...',
                    html: 'Actualizando marca...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {

                    clearValidationErrors('msgError_edit');
                    const token = document.querySelector('input[name="_token"]').value;
                    const formData = new FormData(form);
                    let url = `{{ route('tenant.insumos.marcas.update', ['id' => ':id']) }}`;
                    url = url.replace(':id', parameters.id);

                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'X-HTTP-Method-Override': 'PUT'
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
                        dtCategories.draw();
                        $('#mdlBrandEdit').modal('hide');
                        toastr.success(res.message, 'OPERACIÓN COMPLETADA');
                        Swal.close();
                    } else {
                        toastr.error(res.message, 'ERROR EN EL SERVIDOR');
                        Swal.close();
                    }

                } catch (error) {
                    toastr.error(error, 'ERROR EN LA PETICIÓN ACTUALIZAR MARCA');
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
