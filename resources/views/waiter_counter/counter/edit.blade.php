@extends('layouts.template')

@section('title')
    Platos
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h4 class="card-title mb-md-0 mb-2">EDITAR PLATO</h4>

            <div class="d-flex flex-wrap gap-2">

            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    @include('supply.dishes.forms.form_edit')
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-12 d-flex justify-content-end">

                    <!-- BOTÓN VOLVER -->
                    <button type="button" class="btn btn-danger me-1"
                        onclick="redirect('tenant.abastecimiento.platos.index')">
                        <i class="fas fa-arrow-left"></i> VOLVER
                    </button>

                    <!-- BOTÓN REGISTRAR -->
                    <button class="btn btn-primary" form="form_edit" type="submit">
                        <i class="fas fa-save"></i> ACTUALIZAR
                    </button>

                </div>

            </div>
        </div>
    </div>
@endsection

<style>
    .swal2-container {
        z-index: 9999999;
    }
</style>

@section('js')
    <script>

        document.addEventListener('DOMContentLoaded', () => {
            loadTomSelect();
            loadFilePound();
            loadPreviewData();
            events();
        })

        function events() {
            document.querySelector('#form_edit').addEventListener('submit', (e) => {
                e.preventDefault();
                update(e.target);
            })
        }

        function loadFilePound() {
            const inputImg = document.querySelector('.filepond-input');
            FilePond.create(inputImg, {
                allowImagePreview: true,
                imagePreviewHeight: 120,
                imageCropAspectRatio: '1:1',
                styleLayout: 'compact',
                stylePanelAspectRatio: 0.5,
                storeAsFile: true,
            });
        }

        function loadTomSelect() {
            const typeDishSelect = document.getElementById('type_dish_id');
            if (typeDishSelect && !typeDishSelect.tomselect) {
                window.typeDishSelect = new TomSelect(typeDishSelect, {
                    valueField: 'id',
                    labelField: 'name',
                    searchField: ['name', 'id'],
                    create: false,
                    sortField: {
                        field: 'id',
                        direction: 'desc'
                    },
                    plugins: ['clear_button'],
                    render: {
                        option: (item, escape) => `
                            <div class="d-flex align-items-center">
                                <i class="fas fa-${item.icon ?? 'utensils'} me-1 text-primary"></i>
                                <span>${escape(item.name)}</span>
                            </div>
                        `,
                        item: (item, escape) => `
                            <div class="d-flex align-items-center">
                                <i class="fas fa-${item.icon ?? 'utensils'} me-1 text-primary"></i>
                                <span>${escape(item.name)}</span>
                            </div>
                        `
                    }

                });
            }
        }

        function addColorSelect(item) {
            window.colorSelect.addOption(item);
            window.colorSelect.setValue(item.id);
            window.colorSelect.refreshOptions(false);
        }

        async function update(formEdit) {

            const result = await Swal.fire({
                title: '¿Desea actualizar el plato?',
                text: "Confirme para continuar",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'SI, actualizar',
                cancelButtonText: 'NO',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            });

            if (result.isConfirmed) {
                try {

                    clearValidationErrors('msgError');

                    Swal.fire({
                        title: 'Actualizando plato...',
                        text: 'Por favor espere',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const formData = new FormData(formEdit);
                    formData.append('_method', 'PUT');
                    const id    =   @json($dish->id);

                    const res = await axios.post(route('tenant.abastecimiento.platos.update',{id}), formData);
                    if (res.data.success) {
                        toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                        redirect('tenant.abastecimiento.platos.index');
                    } else {
                        toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                        Swal.close();
                    }

                } catch (error) {
                    Swal.close();
                    if (error.response && error.response.status === 422) {
                        const errors = error.response.data.errors;
                        paintValidationErrors(errors, 'error');
                        return;
                    }
                }

            } else {

                Swal.fire({
                    icon: 'info',
                    title: 'Operación cancelada',
                    text: 'No se realizaron acciones.',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false
                });

            }
        }

        function loadPreviewData() {
            //======== IMAGES =======
            const img = @json($img_route);
            const input = document.querySelector('.filepond-input');

            let pond = FilePond.find(input);
            if (img) {
                pond.addFile(img);
            }
        }
    </script>
@endsection
