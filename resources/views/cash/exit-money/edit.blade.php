@extends('layouts.template')

@section('title')
    Egresos
@endsection

@section('content')
    @include('cash.exit-money.create-supplier-modal')
    @include('cash.exit-money.create-proof-payment-modal')
    @include('utils.modals.cost_center.mdl_create')

    <div class="card">
        @include('cash.exit-money.forms.form_edit_exit')
    </div>
@endsection

@section('js')
    <script>
        let exitDetails = [];
        let counter = 1;

        document.addEventListener('DOMContentLoaded', function() {
            loadPreviewData();
            calcularTotalEgreso();
            events();
            loadSelectsExit();
        });

        function events() {
            eventsMdlCostCenter();


            document.querySelector('#form-create-exit-money').addEventListener('submit', (e) => {
                e.preventDefault();
                updateExitMoney(e.target);
            })

        }

        /* ===============================
           AGREGAR ITEM
        ================================= */
        function addRow() {

            const newItem = {
                id: Date.now(),
                description: '',
                total: 0
            };

            exitDetails.push(newItem);

            renderTable();
        }


        /* ===============================
           RENDERIZAR TABLA
        ================================= */
        function renderTable() {

            const tbody = document.querySelector('#egreso-detail tbody');
            tbody.innerHTML = '';

            exitDetails.forEach((item, index) => {

                const row = `
                <tr>
                    <td class="text-center">${index + 1}</td>

                    <td>
                        <input type="text"
                            class="form-control input-fill"
                            value="${item.description}"
                            oninput="updateDescription(${item.id}, this.value)">
                    </td>

                    <td>
                        <input type="number"
                            step="0.01"
                            class="form-control text-end inputDecimalPositivo input-fill"
                            value="${item.total}"
                            oninput="updateTotal(${item.id}, this.value)">
                    </td>

                    <td class="text-center">
                        <button type="button"
                            class="btn btn-danger btn-sm"
                            onclick="deleteRow(${item.id})">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;

                tbody.insertAdjacentHTML('beforeend', row);
            });

            calcularTotalEgreso();
        }


        /* ===============================
           ACTUALIZAR DESCRIPCIÓN
        ================================= */
        function updateDescription(id, value) {

            const item = exitDetails.find(d => d.id === id);
            if (item) {
                item.description = value.toUpperCase();
            }
        }


        /* ===============================
           ACTUALIZAR TOTAL
        ================================= */
        function updateTotal(id, value) {

            const item = exitDetails.find(d => d.id === id);
            if (item) {
                item.total = parseFloat(value) || 0;
            }

            calcularTotalEgreso();
        }


        /* ===============================
           ELIMINAR ITEM
        ================================= */
        function deleteRow(id) {

            exitDetails = exitDetails.filter(d => d.id !== id);

            renderTable();
        }


        /* ===============================
           CALCULAR TOTAL
        ================================= */
        function calcularTotalEgreso() {
            let total = exitDetails.reduce((sum, item) => {
                return sum + item.total;
            }, 0);

            document.getElementById('total-del-egreso').innerText = total.toFixed(2);
        }


        /* ===============================
           OBTENER DATA PARA ENVIAR
        ================================= */
        function getDetailsForSubmit() {
            return exitDetails;
        }

        function openCreateSupplierModal() {
            $('#createSupplierModal').modal('toggle');
        }

        function openCreateProofPaymentModal() {
            $('#createProofPaymentModal').modal('toggle');
        }

        function validateExitDetails(details) {

            let errors = [];

            if (!Array.isArray(details) || details.length === 0) {
                errors.push('Debe agregar al menos un detalle.');
                return errors;
            }

            for (let i = 0; i < details.length; i++) {

                const item = details[i];

                if (!item.description || item.description.trim() === '') {
                    errors.push(`La descripción está vacía en la fila ${i + 1}.`);
                }

                if (item.total === null || item.total === undefined || item.total <= 0) {
                    errors.push(`El total debe ser mayor a 0 en la fila ${i + 1}.`);
                }

                if (errors.length >= 2) {
                    break;
                }
            }

            return errors;
        }

        async function updateExitMoney(formUpdateExitMoney) {
            toastr.clear();

            const errors = validateExitDetails(exitDetails);
            if (errors.length > 0) {
                toastr.error(errors.join('\n'));
                return;
            }

            const result = await Swal.fire({
                title: '¿Desea actualizar el egreso?',
                text: "Confirmar",
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
                        title: 'Actualizando egreso...',
                        text: 'Por favor espere',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const id = @json($exit_money->id);
                    const formData = new FormData(formUpdateExitMoney);
                    formData.append('_method', 'PUT');
                    formData.append('lstDetails', JSON.stringify(exitDetails));

                    const res = await axios.post(route('tenant.cajas.egresos.update', {
                        id
                    }), formData);

                    if (res.data.success) {
                        toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                        redirect('tenant.cajas.egresos.index');
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

                    toastr.error('Ocurrió un error inesperado', 'ERROR');
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

        function loadSelectsExit() {
            const costCenterSelect = document.getElementById('cost_center');
            if (costCenterSelect && !costCenterSelect.tomselect) {
                window.costCenterSelect = new TomSelect(costCenterSelect, {
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
                            <div>
                                ${escape(item.name)}
                            </div>
                        `,
                        item: (item, escape) => `
                            <div>${escape(item.name)}</div>
                        `
                    }
                });
            }

            const initialSupplier = @json($supplier_formatted);
            window.supplierSelect = new TomSelect('#supplier_id', {
                valueField: 'id',
                labelField: 'full_name',
                searchField: ['full_name'],
                options: [initialSupplier],
                items: [initialSupplier.id],
                plugins: ['clear_button'],
                placeholder: 'Seleccione un proveedor',
                maxOptions: 20,
                create: false,
                preload: false,
                load: async (query, callback) => {
                    if (!query.length) return callback();
                    try {
                        const url = route('tenant.utils.searchSupplier', {
                            q: query
                        });
                        const response = await fetch(url);
                        if (!response.ok) throw new Error('Error al buscar proveedores');
                        const data = await response.json();
                        const results = data.data ?? [];
                        callback(results);
                    } catch (error) {
                        console.error('Error cargando clientes:', error);
                        callback();
                    }
                },
                render: {
                    option: (item, escape) => `
                        <div>
                            <strong>${escape(item.full_name)}</strong><br>
                            <small>${escape(item.email ?? '')}</small>
                        </div>
                    `,
                    item: (item, escape) => `<div>${escape(item.full_name)}</div>`
                }
            });
        }

        function loadPreviewData() {
            const exitDetailsPreview = @json($exit_money_detail);
            let parsedDetails = [];

            exitDetailsPreview.forEach(item => {

                exitDetails.push({
                    id: item.id,
                    description: item.description,
                    total: parseFloat(item.total)
                });

            });
            renderTable();
        }
    </script>
@endsection
