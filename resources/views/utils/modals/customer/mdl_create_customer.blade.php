<div class="modal fade" id="mdlCreateCustomer" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Registrar Cliente</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @include('utils.modals.customer.forms.form_create_customer')
            </div>
            <div class="modal-footer">

                <div class="col-12">

                    <div class="row">
                        <div class="col-12 d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                style="margin-right: 6px;">Cerrar</button>
                            <button class="btn btn-primary btnstoreCustomer" type="submit" form="formStoreCustomer">
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
    let customerParams = {
        documentSearchCustomer: null,
    };

    function loadSelectMdlCustomer() {
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
    }

    function eventsMdlCreateCustomer() {

        loadSelectMdlCustomer();
        setDefaultData();

        window.departmentSelect.on('change', function(value) {
            changeDepartment(value);
        });

        window.provinceSelect.on('change', function(value) {
            changeProvince(value);
        });

        document.querySelector('#formStoreCustomer').addEventListener('submit', (e) => {
            e.preventDefault();
            storeCustomer();
        })

        $('#mdlCreateCustomer').on('hidden.bs.modal', function() {

            document.querySelector('#name').value = '';
            document.querySelector('#address').value = '';
            document.querySelector('#phone').value = '';
            document.querySelector('#email').value = '';


            window.typeDocumentSelect.setValue(1);

            customerParams.documentSearchCustomer = null;
            clearValidationErrors('msgErrorCustomer');

        });

        //======= CONSULTAR API DOCUMENTO DNI ========
        document.querySelector('#btn_search_nro_document').addEventListener('click', () => {

            const nro_document = document.querySelector('#nro_document').value;
            const type_identity_document = document.querySelector('#type_identity_document').value;
            toastr.clear();

            if (type_identity_document != 1 && type_identity_document != 3) {
                toastr.error('SOLO SE PUEDE CONSULTAR TIPO DE DOCUMENTO DNI Y RUC');
                return;
            }

            if (!nro_document) {
                toastr.error('DEBE INGRESAR UN NRO DE DOCUMENTO VÁLIDO');
                return;
            }

            if (type_identity_document == 1) {
                if (nro_document.length != 8) {
                    toastr.error('NRO DE DNI DEBE CONTAR CON 8 DÍGITOS');
                    return;
                }
            }

            if (type_identity_document == 2) {
                if (nro_document.length != 11) {
                    toastr.error('NRO DE RUC DEBE CONTAR CON 11 DÍGITOS');
                    return;
                }
            }

            consultDocument(type_identity_document, nro_document);

        })
    }

    function openMdlNewCustomer() {

        if (isNumeric(customerParams.documentSearchCustomer) && customerParams.documentSearchCustomer) {
            //====== DNI ======
            if (customerParams.documentSearchCustomer.length === 8) {
                window.typeDocumentSelect.setValue(1);
                document.querySelector('#nro_document').value = customerParams.documentSearchCustomer;
                document.querySelector('#btn_search_nro_document').click();
            }
            //========= RUC ========
            if (customerParams.documentSearchCustomer.length === 11) {
                window.typeDocumentSelect.setValue(3);
                document.querySelector('#nro_document').value = customerParams.documentSearchCustomer;
                document.querySelector('#btn_search_nro_document').click();
            }
        }

        $('#mdlCreateCustomer').modal('show');
    }

    //======= CONSULTAR DOCUMENTO IDENTIDAD =====
    async function consultDocument(type_identity_document, nro_document) {
        mostrarAnimacion1();
        try {
            const token = document.querySelector('input[name="_token"]').value;

            const url = route('tenant.ventas.clientes.consult_document', {
                type_identity_document: type_identity_document,
                nro_document: nro_document
            })

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': token
                },
            });

            const res = await response.json();

            if (res.success) {

                if (type_identity_document == 1) {
                    setDataDni(res.data);
                }

                if (type_identity_document == 3) {
                    setDataRuc(res.data);
                }

                toastr.info(res.message);
            } else {
                toastr.error(res.message);
            }
        } catch (error) {
            toastr.error(error, 'Error al consultar documento');
        } finally {
            ocultarAnimacion1();
        }
    }

    function setDataDni(data) {

        const full_name = `${data.nombres} ${data.apellido_paterno} ${data.apellido_materno}`;
        const address = data.direccion;

        document.querySelector('#name').value = full_name;
        document.querySelector('#address').value = address;

    }

    function setDataRuc(data) {
        const nombre_o_razon_social = `${data.nombre_o_razon_social}`;
        const direccion_completa = data.direccion_completa;

        document.querySelector('#name').value = nombre_o_razon_social;
        document.querySelector('#address').value = direccion_completa;

        //======= ESTABLECIENDO UBIGEO =====
        const ubigeo = data.ubigeo;
        const ubigeo_department_id = ubigeo[0];
        const ubigeo_province_id = ubigeo[1];
        const ubigeo_district_id = ubigeo[2];

        if (!ubigeo_department_id || !ubigeo_province_id || !ubigeo_district_id) {
            toastr.info('NO SE OBTUVO EL UBIGEO!!!');
            return;
        }

        window.departmentSelect.setValue(parseInt(ubigeo_department_id));
        window.provinceSelect.setValue(parseInt(ubigeo_province_id));
        window.districtSelect.setValue(parseInt(ubigeo_district_id));

    }


    function changeTypeIdentityDocument(type_identity_document_id) {
        const inputNroDocument = document.querySelector('#nro_document');
        const btnSearchNroDocument = document.querySelector('#btn_search_nro_document');

        inputNroDocument.value = '';

        //======== DNI ========
        if (type_identity_document_id === '1') {
            inputNroDocument.maxLength = 8;
            inputNroDocument.disabled = false;
            inputNroDocument.classList.add('inputEnteroPositivo');
            btnSearchNroDocument.classList.remove('d-none');
        }

        //======== RUC =======
        if (type_identity_document_id === '3') {
            inputNroDocument.maxLength = 11;
            inputNroDocument.disabled = false;
            inputNroDocument.classList.add('inputEnteroPositivo');
            btnSearchNroDocument.classList.remove('d-none');
        }

        //======== CARNET EXTRANJERÍA U OTROS DOCUMENTOS =======
        if (type_identity_document_id === '2' || type_identity_document_id === '4' ||
            type_identity_document_id === '5' || type_identity_document_id === '6') {

            inputNroDocument.maxLength = 20;
            inputNroDocument.disabled = false;
            inputNroDocument.classList.remove('inputEnteroPositivo');
            btnSearchNroDocument.classList.add('d-none');

        }
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

            lstProvincesFiltered.forEach((province) => {
                $('#province').append(new Option(province.name, province.id, false, false));
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

    function storeCustomer() {

        Swal.fire({
            title: "DESEA REGISTRAR EL CLIENTE?",
            text: "Se creará un nuevo Cliente!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "SÍ, REGISTRAR!",
            cancelButtonText: "NO, CANCELAR!",
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {

                clearValidationErrors('msgErrorCustomer');

                const token = document.querySelector('input[name="_token"]').value;
                const formStoreCustomer = document.querySelector('#formStoreCustomer');
                const formData = new FormData(formStoreCustomer);
                const urlStoreCustomer = @json(route('tenant.ventas.clientes.store'));

                Swal.fire({
                    title: 'Cargando...',
                    html: 'Registrando nuevo cliente...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    const response = await fetch(urlStoreCustomer, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        body: formData
                    });

                    const res = await response.json();

                    if (response.status === 422) {
                        if ('errors' in res) {
                            paintValidationErrors(res.errors, 'error_customer');
                        }
                        Swal.close();
                        return;
                    }

                    if (res.success) {

                        const customerNew = res.customer;
                        setCustomerNew(customerNew);

                        $('#mdlCreateCustomer').modal('hide');
                        toastr.success(res.message, 'OPERACIÓN COMPLETADA');
                        Swal.close();
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


    function setCustomerNew(customerNew) {
        const option = {
            id: customerNew.id,
            full_name: `${customerNew.type_document_abbreviation}:${customerNew.document_number} - ${customerNew.name}`,
            email: customerNew.email ?? ''
        };

        let instanceSelect = null;
        const customerSelect = getCustomerSelect();

        if (window.clientSelect) {
            instanceSelect = window.clienteSelect;
        }
        if (customerSelect) {
            instanceSelect = customerSelect;
        }

        console.log('instance', instanceSelect);

        if (!instanceSelect.options[option.id]) {
            instanceSelect.addOption(option);
        }
        instanceSelect.setValue(option.id);
    }

    function setDefaultData() {
        const departmentId = parseInt(@json($company_invoice->department_id));
        const provinceId = parseInt(@json($company_invoice->province_id));
        const districtId = parseInt(@json($company_invoice->district_id));

        if (departmentId && provinceId && districtId) {

            window.departmentSelect.setValue(departmentId);
            changeDepartment(departmentId);

            window.provinceSelect.setValue(provinceId);
            changeProvince(provinceId);

            window.districtSelect.setValue(districtId);
        }
    }
</script>
