@extends('layouts.template')

@section('title')
    Platos
@endsection

@push('js-head')
    @vite(['resources/js/libs/filepond.js'])
@endpush

@section('content')
    @include('utils.modals.types_dish.mdl_create');
    @include('utils.modals.mdl_select_consumable.main', [
        'brands' => $brands ?? [],
        'categories' => $categories ?? [],
    ])
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

@section('js')
    <script>
        const paramsCreate = {
            lstSheet: []
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadTomSelect();
            loadFilePound();
            events();
            eventsMdlTypeDish();
            loadMdlSelectConsumable({
                onSelect: onSelectConsumable
            });
        })

        function events() {
            document.querySelector('#form_create').addEventListener('submit', (e) => {
                e.preventDefault();
                store(e.target);
            })

            document.querySelector('#tbl-technical-sheet').addEventListener('input', (e) => {
                if (e.target.classList.contains('sheet-item')) {
                    actionInputSheet(e);
                }
            })

            document.querySelector('#tbl-technical-sheet').addEventListener('click', (e) => {
                const btnDelete = e.target.closest('.btn-delete-sheet');
                if (btnDelete) {
                    actionBtnDeleteSheet(btnDelete);
                }
            })

        }

        function actionBtnDeleteSheet(btnDelete) {
            const id = btnDelete.getAttribute('data-item');
            const index = paramsCreate.lstSheet.findIndex(s => s.id == id);
            if (index === -1) {
                toastr.error('El insumo no existe en la ficha');
                return;
            }
            paramsCreate.lstSheet.splice(index, 1);
            paintTblSheet();
        }

        function onSelectConsumable(itemSelected) {
            toastr.clear();
            const validation = validationSheet(itemSelected);
            if (!validation) return false;
            addSheet(itemSelected);
            paintTblSheet();
            return validation;
        }

        function validationSheet(item) {
            const exists = paramsCreate.lstSheet.findIndex(s => s.id == item.item_id);
            if (exists != -1) {
                toastr.error('El insumo ya existe en la ficha');
                return false;
            }
            return true;
        }

        function addSheet(item) {
            const _item = {
                id: item.item_id,
                quantity: 1,
                name: item.item_name,
                unit_name: item.unit_name
            };
            paramsCreate.lstSheet.push(_item);
        }

        function paintTblSheet() {
            const tbody = document.querySelector('#tbl-technical-sheet tbody');
            let rows = ``;
            paramsCreate.lstSheet.forEach((s) => {
                rows += `<tr>
                            <td>
                                <button type="button" class="btn btn-danger btn-delete-sheet" data-item="${s.id}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                            <td>${s.name}</td>
                            <td>
                                <input maxlength="6" class="form-control input-fill sheet-item inputDecimalPositivo" data-item="${s.id}" value="${s.quantity}" placeholder="Cant">
                                </input>
                            </td>
                            <td>${s.unit_name}</td>
                        </tr>`
            })
            tbody.innerHTML = rows;
        }

        function actionInputSheet(e) {
            toastr.clear();
            const value = parseFloat(e.target.value);
            const id = e.target.getAttribute('data-item');

            console.log('value', value);
            updateSheet(id, value);
        }

        function updateSheet(id, value) {
            const index = paramsCreate.lstSheet.findIndex(s => s.id == id);
            if (index === -1) {
                toastr.error('El item no existe en la ficha');
                return;
            }
            paramsCreate.lstSheet[index].quantity = value;
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

                    const formData = new FormData(formCreate);
                    formData.append('lstSheet', JSON.stringify(paramsCreate.lstSheet));
                    const res = await axios.post(route('tenant.abastecimiento.platos.store'), formData);
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
