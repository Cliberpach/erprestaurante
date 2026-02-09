@extends('layouts.template')
@section('title')
    REGISTRAR CLIENTE
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h4 class="card-title mb-md-0 mb-2">REGISTRAR CLIENTE</h4>

            <div class="d-flex flex-wrap gap-2">

            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    @include('sales.customers.forms.form_create')
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-12 d-flex justify-content-end">

                    <!-- BOTÓN VOLVER -->
                    <button type="button" class="btn btn-danger me-1" onclick="redirect('tenant.ventas.clientes.index')">
                        <i class="fas fa-arrow-left"></i> VOLVER
                    </button>

                    <!-- BOTÓN REGISTRAR -->
                    <button class="btn btn-primary" form="formRegistrarCliente" type="submit">
                        <i class="fas fa-save"></i> REGISTRAR
                    </button>

                </div>

            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            loadSelectCustomers();
            events();
            configDefault();
        })

        function events() {

            //===== CHECK PERMITIR VENTAS AL CRÉDITO =======
            /*document.querySelector('#control_credito').addEventListener('change', (e) => {
                const marcado = e.target.checked;
                const inputLimite = document.querySelector('#limite_credito');
                if (marcado) {
                    inputLimite.readOnly = false;
                    inputLimite.classList.add('colorReadOnly');
                } else {
                    inputLimite.readOnly = true;
                    inputLimite.classList.remove('colorReadOnly');
                    inputLimite.value = 0;
                }
            })*/

            window.departmentSelect.on('change', function(value) {
                changeDepartment(value);
            });

            window.provinceSelect.on('change', function(value) {
                changeProvince(value);
            });

            //======= CONSULTAR API DOCUMENTO DNI ========
            document.querySelector('#btn_consultar_documento').addEventListener('click', () => {

                const nro_documento = document.querySelector('#nro_document').value;
                const tipo_documento = document.querySelector('#type_identity_document').value;
                toastr.clear();

                if (tipo_documento != 1 && tipo_documento != 3) {
                    toastr.error('SOLO SE PUEDE CONSULTAR DNI Y RUC');
                    return;
                }

                if (tipo_documento == 1 && nro_documento.length != 8) {
                    toastr.error('NRO DE DNI DEBE CONTAR CON 8 DÍGITOS');
                    return;
                }

                if (tipo_documento == 3 && nro_documento.length != 11) {
                    toastr.error('NRO DE RUC DEBE CONTAR CON 11 DÍGITOS');
                    return;
                }

                consultarDocumento(tipo_documento, nro_documento);

            })

            document.querySelector('#formRegistrarCliente').addEventListener('submit', (e) => {
                e.preventDefault();
                storeCustomer();
            })

            document.addEventListener('click', (e) => {
                if (e.target.closest('.btnVolver')) {
                    const rutaIndex = '{{ route('tenant.ventas.clientes.index') }}';
                    window.location.href = rutaIndex;
                }
            })

        }

        function loadSelectCustomers() {
            const typeDocumentSelect = document.getElementById('type_identity_document');
            if (typeDocumentSelect && !typeDocumentSelect.tomselect) {
                window.typeDocumentSelect = new TomSelect(typeDocumentSelect, {
                    valueField: 'id',
                    labelField: 'description',
                    searchField: ['description', 'id'],
                    create: false,
                    sortField: {
                        field: 'id',
                        direction: 'desc'
                    },
                    plugins: ['clear_button'],
                    render: {
                        option: (item, escape) => `
                            <div>
                                ${escape(item.description)}
                            </div>
                        `,
                        item: (item, escape) => `
                            <div>${escape(item.description)}</div>
                        `
                    }
                });
            }

            const departmentSelect = document.getElementById('department');
            if (departmentSelect && !departmentSelect.tomselect) {
                window.departmentSelect = new TomSelect(departmentSelect, {
                    valueField: 'id',
                    labelField: 'description',
                    searchField: ['description', 'id'],
                    create: false,
                    sortField: {
                        field: 'id',
                        direction: 'desc'
                    },
                    plugins: ['clear_button'],
                    render: {
                        option: (item, escape) => `
                            <div>
                                ${escape(item.description)}
                            </div>
                        `,
                        item: (item, escape) => `
                            <div>${escape(item.description)}</div>
                        `
                    }
                });
            }

            const provinceSelect = document.getElementById('province');
            if (provinceSelect && !provinceSelect.tomselect) {
                window.provinceSelect = new TomSelect(provinceSelect, {
                    valueField: 'id',
                    labelField: 'description',
                    searchField: ['description', 'id'],
                    create: false,
                    sortField: {
                        field: 'id',
                        direction: 'desc'
                    },
                    plugins: ['clear_button'],
                    render: {
                        option: (item, escape) => `
                            <div>
                                ${escape(item.description)}
                            </div>
                        `,
                        item: (item, escape) => `
                            <div>${escape(item.description)}</div>
                        `
                    }
                });
            }

            const districtSelect = document.getElementById('district');
            if (districtSelect && !districtSelect.tomselect) {
                window.districtSelect = new TomSelect(districtSelect, {
                    valueField: 'id',
                    labelField: 'description',
                    searchField: ['description', 'id'],
                    create: false,
                    sortField: {
                        field: 'id',
                        direction: 'desc'
                    },
                    plugins: ['clear_button'],
                    render: {
                        option: (item, escape) => `
                            <div>
                                ${escape(item.description)}
                            </div>
                        `,
                        item: (item, escape) => `
                            <div>${escape(item.description)}</div>
                        `
                    }
                });
            }
        }

        function configDefault() {
            //======== CHK PERMITIR VENTAS AL CRÉDITO ======
            /*const chkPermitirCredito = document.querySelector('#control_credito');
            chkPermitirCredito.checked = false;
            chkPermitirCredito.dispatchEvent(new Event('change'));*/

            //======== SELECT TIPO DOCUMENTO ======
            window.typeDocumentSelect.setValue(1);
        }

        function storeCustomer() {
            Swal.fire({
                title: "DESEA REGISTRAR EL CLIENTE?",
                text: "Se creará un nuevo cliente!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "SÍ, REGISTRAR!",
                cancelButtonText: "NO, CANCELAR!",
                reverseButtons: true
            }).then(async (result) => {
                if (result.isConfirmed) {

                    clearValidationErrors('msgError');
                    const token = document.querySelector('input[name="_token"]').value;
                    const formRegistrarCliente = document.querySelector('#formRegistrarCliente');
                    const formData = new FormData(formRegistrarCliente);
                    const urlRegistrarCliente = @json(route('tenant.ventas.clientes.store'));

                    Swal.fire({
                        title: 'Cargando...',
                        html: 'Registrando nuevo cliente...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    try {
                        const response = await fetch(urlRegistrarCliente, {
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
                            const cliente_index = @json(route('tenant.ventas.clientes.index'));
                            toastr.success(res.message, 'OPERACIÓN COMPLETADA');
                            window.location.href = cliente_index;
                        } else {
                            toastr.error(res.message, 'ERROR EN EL SERVIDOR');
                            Swal.close();
                        }


                    } catch (error) {
                        toastr.error(error, 'ERROR EN LA PETICIÓN REGISTRAR CLIENTE');
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

        //======== CHANGE TIPO DOCUMENTO ======
        function changeTipoDoc(params) {
            const tipo_documento = document.querySelector('#type_identity_document').value;
            const inputNroDoc = document.querySelector('#nro_document');
            const btnConsultarDocumento = document.querySelector('#btn_consultar_documento');

            //======== DNI =======
            if (tipo_documento == 1) {
                inputNroDoc.value = '';
                inputNroDoc.readOnly = false;
                inputNroDoc.maxLength = 8;
                btnConsultarDocumento.disabled = false;
                inputNroDoc.classList.add('inputEnteroPositivo');
            }

            //======== RUC =======
            if (tipo_documento == 3) {
                inputNroDoc.value = '';
                inputNroDoc.readOnly = false;
                inputNroDoc.maxLength = 11;
                btnConsultarDocumento.disabled = false;
                inputNroDoc.classList.add('inputEnteroPositivo');
            }

            //====== CARNET EXTRANJERÍA =====
            if (tipo_documento == 2 || (tipo_documento != 1 && tipo_documento != 3)) {
                inputNroDoc.value = '';
                inputNroDoc.readOnly = false;
                inputNroDoc.maxLength = 20;
                btnConsultarDocumento.disabled = true;
                inputNroDoc.classList.remove('inputEnteroPositivo');
            }
        }

        //======= CONSULTAR DOCUMENTO IDENTIDAD =====
        async function consultarDocumento(typeDocument, nroDocument) {
            mostrarAnimacion1();
            try {
                const token = document.querySelector('input[name="_token"]').value;
                const baseUrlCliente = @json(route('tenant.ventas.clientes.consult_document'));

                const url = route('tenant.ventas.clientes.consult_document', {
                    type_identity_document: typeDocument,
                    nro_document: nroDocument
                })

                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                });

                const res = await response.json();

                if (res.success) {

                    if (!res.success) {
                        toastr.error(res.data.message);
                        return;
                    }
                    if (typeDocument == 1) {
                        setDatosDni(res.data);
                    }
                    if (typeDocument == 3) {
                        setDatosRuc(res.data);
                    }

                    toastr.info('OPERACIÓN COMPLETADA', res.message);
                } else {
                    toastr.error(res.message, 'ERROR EN EL SERVIDOR AL CONSULTAR DOCUMENTO');
                }
            } catch (error) {
                toastr.error(error, 'ERROR EN LA PETICIÓN CONSULTAR DOCUMENTO');
            } finally {
                ocultarAnimacion1();
            }
        }

        /*
        ubigeo:
        [
          "01",
          "0101",
          "010101"
        ]
        */
        function setDatosRuc(data) {
            const nombre_o_razon_social = `${data.nombre_o_razon_social}`;
            const direccion_completa = data.direccion_completa;
            const ubigeo = data.ubigeo;

            document.querySelector('#name').value = nombre_o_razon_social;
            document.querySelector('#address').value = direccion_completa;

            //======= COLOCANDO UBIGEO =======
            let departmentId = parseInt(ubigeo[0]);
            let provinceId = parseInt(ubigeo[1]);
            let districtId = parseInt(ubigeo[2]);


            if (!isNaN(departmentId)) {
                window.departmentSelect.setValue(departmentId);
            }

            if (!isNaN(provinceId)) {
                window.provinceSelect.setValue(provinceId);
            }

            if (!isNaN(districtId)) {
                window.districtSelect.setValue(districtId);
            }

        }

        function setDatosDni(data) {
            const nombre_completo = `${data.nombres} ${data.apellido_paterno} ${data.apellido_materno}`;
            const direccion = data.direccion;

            document.querySelector('#name').value = nombre_completo;
            document.querySelector('#address').value = direccion;
        }

        function changeDepartment(department_id) {
            const lstProvinces = @json($provinces);
            const lstDistricts = @json($districts);
            let lstProvincesFiltered = [];

            if (department_id) {

                department_id = String(department_id).padStart(2, '0');

                lstProvincesFiltered = lstProvinces.filter((province) => {
                    return province.department_id == department_id;
                })

                window.provinceSelect.clear();
                window.provinceSelect.clearOptions();
                window.provinceSelect.addOptions(
                    lstProvincesFiltered.map(province => ({
                        id: province.id,
                        description: province.name,
                    }))
                );
                window.provinceSelect.refreshOptions(false);

                window.districtSelect.clear();
                window.districtSelect.clearOptions();
            }
        }

        function changeProvince(province_id) {

            const lstDistricts = @json($districts);

            let lstDistrictsFiltered = [];

            if (province_id) {

                province_id = String(province_id).padStart(4, '0');

                lstDistrictsFiltered = lstDistricts.filter((district) => {
                    return district.province_id == province_id;
                })

                window.districtSelect.clear();
                window.districtSelect.clearOptions();
                window.districtSelect.addOptions(
                    lstDistrictsFiltered.map(district => ({
                        id: district.id,
                        description: district.name,
                    }))
                );
                window.districtSelect.refreshOptions(false);
            }
        }
    </script>
@endsection
