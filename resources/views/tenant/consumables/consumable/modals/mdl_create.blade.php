<div class="modal fade" id="mdlCreateConsumable" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Registrar Insumo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                @include('tenant.consumables.consumable.forms.form_create')
            </div>

            <div class="modal-footer flex-column align-items-stretch">

                <div class="d-flex justify-content-end w-100 gap-2">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cerrar
                    </button>

                    <button form="formCreateConsumable" type="submit" class="btn btn-primary">
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

@push('js-script')
    <script>
        let pondImg = null;

        function openMdlCreate() {
            $('#mdlCreateConsumable').modal('show');
        }

        function eventsMdlCreateConsumable() {
            loadFilePound();

            document.querySelector('#formCreateConsumable').addEventListener('submit', (e) => {
                e.preventDefault();
                storeConsumable(e.target);
            })

            document.querySelector('#image').addEventListener('change', function(event) {
                const file = event.target.files[0];
                const reader = new FileReader();
                if (file) {

                    reader.onload = function(e) {
                        document.getElementById('img_vista_previa').src = e.target.result;
                    };

                    reader.readAsDataURL(file);
                } else {
                    document.getElementById('img_vista_previa').src = @json(asset('assets/img/products/img_default.png'));
                }
            });

            document.addEventListener('click', (e) => {
                //======== LIMPIAR IMAGEN =======
                if (e.target.closest('.btnSetImgDefault')) {
                    const inputImgPreview = document.querySelector('#img_vista_previa');
                    inputImgPreview.src = @json(asset('assets/img/products/img_default.png'));

                    const inputCargarImg = document.querySelector('#image');
                    inputCargarImg.value = '';
                }
            })

            $('#mdlCreateConsumable').on('hidden.bs.modal', function() {
                clearMdlCreateConsumable();
            });
        }

        function loadFilePound() {
            const inputImg = document.querySelector('#image');

            pondImg = FilePond.create(inputImg, {
                allowImagePreview: true,
                imagePreviewHeight: 120,
                imageCropAspectRatio: '1:1',
                styleLayout: 'compact',
                stylePanelAspectRatio: 0.5,
                storeAsFile: true,

                maxFileSize: '2MB',
                acceptedFileTypes: [
                    'image/jpeg',
                    'image/png',
                    'image/webp',
                    'image/avif'
                ],
                labelFileTypeNotAllowed: 'Formato no permitido',
                labelMaxFileSizeExceeded: 'El archivo supera los 2 MB',
            });
        }

        function storeConsumable(formstoreConsumable) {
            Swal.fire({
                title: "DESEA REGISTRAR EL INSUMO?",
                text: "Se creará un nuevo insumo!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "SÍ, REGISTRAR!",
                cancelButtonText: "NO, CANCELAR!",
                reverseButtons: true
            }).then(async (result) => {
                if (result.isConfirmed) {

                    clearValidationErrors('msgError');
                    const token = document.querySelector('input[name="_token"]').value;
                    const formData = new FormData(formstoreConsumable);
                    const urlstoreConsumable = @json(route('tenant.insumos.insumos.store'));

                    Swal.fire({
                        title: 'Cargando...',
                        html: 'Registrando nuevo insumo...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    try {
                        const response = await fetch(urlstoreConsumable, {
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
                            dtConsumables.ajax.reload();
                            toastr.success(res.message, 'OPERACIÓN COMPLETADA');
                            $('#mdlCreateConsumable').modal('hide');
                        } else {
                            toastr.error(res.message, 'ERROR EN EL SERVIDOR');
                        }

                    } catch (error) {
                        toastr.error(error, 'ERROR EN LA PETICIÓN REGISTRAR INSUMO');
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

        function clearMdlCreateConsumable() {
            document.querySelector('#name').value = '';
            document.querySelector('#description').value = '';
            document.querySelector('#sale_price').value = '1';
            document.querySelector('#purchase_price').value = '1';
            document.querySelector('#stock').value = '0';
            document.querySelector('#stock_min').value = '1';
            document.querySelector('#code_factory').value = '';
            document.querySelector('#code_bar').value = '';
            window.categorySelect.clear();
            window.brandSelect.clear();
            window.unitSelect.clear();
            setText(window.categorySelect, 'REPUESTO');
            setText(window.brandSelect, 'NACIONAL');
            setText(window.unitSelect, 'NIU-UNIDAD');
            if (pondImg) {
                pondImg.removeFiles();
            }
        }
    </script>
@endpush
