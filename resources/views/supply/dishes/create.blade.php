@extends('layouts.template')

@section('title')
    Platos
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h4 class="card-title mb-md-0 mb-2">REGISTRAR PLATO</h4>

            <div class="d-flex flex-wrap gap-2">

            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    @include('supply.dishes.forms.form_create')
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
                    <button class="btn btn-primary" form="form_create" type="submit">
                        <i class="fas fa-save"></i> REGISTRAR
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
        let dtYears = null;

        document.addEventListener('DOMContentLoaded', () => {
            loadTomSelect();
            loadFilePound();
            events();
        })

        function events() {
            document.querySelector('#form_create').addEventListener('submit', (e) => {
                e.preventDefault();
                store(e.target);
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

        async function accionBuscarPlaca() {
            const placa = document.querySelector('#plate').value.trim();

            if (placa.length < 6 || placa.length > 8) {
                toastr.error('LA PLACA DEBE TENER ENTRE 6 Y 8 CARACTERES');
                return;
            }

            searchPlate(placa);

        }

        async function searchPlate(placa) {
            mostrarAnimacion1();
            try {
                toastr.clear();
                const res = await axios.get(route('tenant.utils.searchPlate', placa));
                if (res.data.success) {

                    if (res.data.origin == 'BD') {
                        toastr.error('VEHICULO YA EXISTE EN BD');
                        return;
                    }

                    const dataApi = res.data.data;
                    if (dataApi.mensaje == 'SUCCESS') {
                        toastr.info(dataApi.mensaje);
                        setDataApi(res);
                    }
                } else {
                    toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                }
            } catch (error) {
                toastr.error(error, 'ERROR EN LA PETICIÓN CONSULTAR PLACA');
            } finally {
                ocultarAnimacion1();
            }
        }

        function setDataApi(res) {

            const dataApi = res.data.data.data;
            const model = res.data.model;
            const color = res.data.color;

            const mensaje = dataApi.mensaje;
            if (mensaje == 'No encontrado') {
                toastr.error(mensaje);
                return;
            }

            const modelItem = {
                id: model.id,
                text: `${dataApi.marca}-${dataApi.modelo}`
            };
            addModelSelect(modelItem);

            if (dataApi.color) {
                const colorItem = {
                    id: color.id,
                    description: `${dataApi.color}`
                };
                addColorSelect(colorItem);
            }

            document.querySelector('#vin').value = dataApi.vin;
            document.querySelector('#serie').value = dataApi.serie;

        }

        function addModelSelect(item) {
            window.modelSelect.clear();
            window.modelSelect.clearOptions();
            window.modelSelect.addOption(item);
            window.modelSelect.setValue(item.id);
        }

        function addColorSelect(item) {
            window.colorSelect.addOption(item);
            window.colorSelect.setValue(item.id);
            window.colorSelect.refreshOptions(false);
        }

        async function store(formCreate) {

            const result = await Swal.fire({
                title: '¿Desea registrar el plato?',
                text: "Confirme para continuar",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'SI, registrar',
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
                        title: 'Registrando plato...',
                        text: 'Por favor espere',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const res = await axios.post(route('tenant.abastecimiento.platos.store'), formCreate);
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
    </script>
@endsection
