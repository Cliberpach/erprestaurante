@extends('layouts.template')

@section('title')
    Editar Empresa
@endsection

@push('name')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
    @include('company.modals.modal_numeration')

    <div class="row">

        <div class="col-12 mb-3">

            <div class="card shadow-sm">

                <!-- HEADER CLICKABLE -->
                <div class="card-header bg-primary d-flex justify-content-between align-items-center text-white"
                    data-bs-toggle="collapse" data-bs-target="#collapseCompanyData" aria-expanded="true"
                    style="cursor:pointer;">

                    <div>
                        <i class="fas fa-building me-2"></i>
                        <strong>Datos Empresa</strong>
                    </div>

                    <!-- PALOMITA -->
                    <i class="fas fa-chevron-down transition" id="iconCompanyData"></i>
                </div>

                <!-- BODY COLLAPSABLE -->
                <div id="collapseCompanyData" class="show collapse">
                    <div class="card-body">
                        @include('company.forms.form_edit_company_tenant')
                    </div>

                    <div class="card-footer bg-light">
                        <p class="fw-bold text-warning mb-0">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Los campos con (*) son obligatorios
                        </p>
                    </div>
                </div>

            </div>

        </div>


        <div class="col-12 mb-3">

            <div class="card shadow-sm">

                <!-- HEADER CLICKABLE -->
                <div class="card-header bg-primary d-flex justify-content-between align-items-center text-white"
                    data-bs-toggle="collapse" data-bs-target="#collapseBillingData" aria-expanded="false"
                    style="cursor:pointer;">

                    <div>
                        <i class="fas fa-file-invoice-dollar me-2"></i>
                        <strong>Facturación Credenciales</strong>
                    </div>

                    <!-- PALOMITA -->
                    <i class="fas fa-chevron-down transition" id="iconBillingData"></i>
                </div>

                <!-- BODY COLLAPSABLE -->
                <div id="collapseBillingData" class="collapse">
                    <div class="card-body">
                        @include('company.forms.form_edit_billing_tenant')
                    </div>

                    <div class="card-footer bg-light">
                        <div class="row align-items-center">
                            <div class="col-md-6 col-12 mb-md-0 mb-2">
                                <p class="fw-bold text-warning mb-0">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Los campos con (*) son obligatorios
                                </p>
                            </div>

                            <div class="col-md-6 col-12 text-md-end">
                                <button class="btn btn-primary btnstoreCustomer" type="submit" form="form_edit_billing">
                                    <i class="fas fa-floppy-disk me-1"></i>
                                    GUARDAR
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>


        <div class="col-12 mb-3">

            <div class="card shadow-sm">

                <!-- HEADER -->
                <div class="card-header bg-primary d-flex justify-content-between align-items-center text-white"
                    data-bs-toggle="collapse" data-bs-target="#collapseNumeration" aria-expanded="false"
                    style="cursor:pointer;">

                    <div>
                        <i class="fas fa-hashtag me-2"></i>
                        <strong>Facturación Numeración</strong>
                    </div>

                    <!-- PALOMITA -->
                    <i class="fas fa-chevron-down transition" id="iconNumeration"></i>
                </div>

                <!-- COLLAPSABLE BODY (CERRADO POR DEFECTO) -->
                <div id="collapseNumeration" class="collapse">
                    <div class="card-body">

                        <!-- BOTÓN NUEVO -->
                        <div class="row mb-3">
                            <div class="col">
                                <button class="btn btn-primary btn-sm" onclick="openMdlNumeration()">
                                    <i class="fas fa-plus me-1"></i> NUEVO
                                </button>
                            </div>
                        </div>

                        <!-- TABLA -->
                        <div class="row">
                            <div class="col-12">
                                @include('company.tables.tbl_numeration')
                            </div>
                        </div>

                    </div>

                    <!-- FOOTER -->
                    <div class="card-footer bg-light">
                        <p class="fw-bold text-warning mb-0">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Los campos con (*) son obligatorios
                        </p>
                    </div>
                </div>

            </div>

        </div>



    </div>
@endsection

@section('js')
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAS6qv64RYCHFJOygheJS7DvBDYB0iV2wI&libraries=geometry,places">
    </script>

    <script>
        let map;
        let onemarker = 0;



        document.addEventListener('DOMContentLoaded', function() {
            iniciarSelect2();
            loadTomSelect();
            startDataTableNumeration();
            setUbigeoPreview();
            setMapa();
            events();
        });

        function events() {

            eventsMdlNumeration();

            document.querySelector('#form_edit_billing').addEventListener('submit', (e) => {
                e.preventDefault();
                updateInvoiceCompany();
            })

            document.getElementById("formEditCompanyTenant").addEventListener("keydown", function(event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                }
            });

        }

        function iniciarSelect2() {
            $('.select2_form_numeration').select2({
                theme: "bootstrap-5",
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                placeholder: $(this).data('placeholder'),
                dropdownParent: $('#mdlNumeration')
            })
        }

        function loadTomSelect() {

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

        function setMapa() {

            const lat = @json($company->lat);
            const lng = @json($company->lng);

            console.log('latitud', parseFloat(lat));

            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 12,
                center: {
                    lat: lat ? parseFloat(lat) : -8.1092027,
                    lng: lng ? parseFloat(lng) : -79.0244529
                },
                gestureHandling: "greedy",
                zoomControl: false,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: false,
            });

            if (!isNaN(parseFloat(lat)) && !isNaN(parseFloat(lng))) {
                editmarker(parseFloat(lat), parseFloat(lng))
            }

            google.maps.event.addListener(map, "click", function(event) {
                if (onemarker == 0) {
                    var marker = new google.maps.Marker({
                        position: event.latLng,
                        map: map,
                        draggable: true
                    });
                    $("#lat").val(marker.getPosition().lat());
                    $("#lng").val(marker.getPosition().lng());
                    google.maps.event.addListener(marker, "dragend", function(event) {
                        $("#lat").val(this.getPosition().lat());
                        $("#lng").val(this.getPosition().lng());
                    });
                    onemarker = 1;
                }
            });

            const input = document.getElementById("searchBox");
            const autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo("bounds", map);


            autocomplete.addListener("place_changed", () => {
                const place = autocomplete.getPlace();

                if (!place.geometry || !place.geometry.location) {
                    alert("No se encontró el lugar. Intenta con otra búsqueda.");
                    return;
                }

                // Centrar el mapa en la ubicación seleccionada
                map.setCenter(place.geometry.location);
                map.setZoom(20);

                // Agregar marcador en la ubicación seleccionada

                var marker = new google.maps.Marker({
                    position: place.geometry.location,
                    map: map,
                    draggable: true
                });
                $("#lat").val(marker.getPosition().lat());
                $("#lng").val(marker.getPosition().lng());

                google.maps.event.addListener(marker, "dragend", function(event) {
                    $("#lat").val(this.getPosition().lat());
                    $("#lng").val(this.getPosition().lng());
                });

                onemarker = 1;

            });

        }

        function editmarker(lat, lng) {
            var marker = new google.maps.Marker({
                position: {
                    lat: lat,
                    lng: lng
                },
                map: map,
                draggable: true
            })
            $("#lat").val(marker.getPosition().lat());
            $("#lng").val(marker.getPosition().lng());
            google.maps.event.addListener(marker, "dragend", function(event) {
                $("#lat").val(this.getPosition().lat());
                $("#lng").val(this.getPosition().lng());
            });
            onemarker = 1;
        }

        function setUbigeoPreview() {

            const departmentSelect = document.getElementById("department");
            if (departmentSelect) {
                departmentSelect.dispatchEvent(new Event("change"));
            }

            window.provinceSelect.setValue("{{ $company_invoice->province_id }}");
            window.districtSelect.setValue("{{ $company_invoice->district_id }}");

        }

        function updateInvoiceCompany() {
            Swal.fire({
                title: "DESEA ACTUALIZAR LOS DATOS DE FACTURACIÓN DE LA EMPRESA?",
                text: "Esto producirá cambios en la facturación!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "SÍ, ACTUALIZAR!",
                cancelButtonText: "NO, CANCELAR!",
                reverseButtons: true
            }).then(async (result) => {
                if (result.isConfirmed) {

                    Swal.fire({
                        title: 'Cargando...',
                        html: 'Actualizando facturación de la empresa...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });


                    try {

                        clearValidationErrors('msgError');
                        const token = document.querySelector('input[name="_token"]').value;
                        const form_edit_billing = document.querySelector('#form_edit_billing');
                        const formData = new FormData(form_edit_billing);

                        const id = @json($company->id);
                        let urlUpdateInvoiceCompany =
                            `{{ route('tenant.mantenimiento.empresas.updateInvoice', ['id' => ':id']) }}`;
                        urlUpdateInvoiceCompany = urlUpdateInvoiceCompany.replace(':id', id);

                        const response = await fetch(urlUpdateInvoiceCompany, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'X-HTTP-Method-Override': 'PUT'
                            },
                            body: formData
                        });

                        const res = await response.json();

                        if (response.status === 422) {
                            if ('errors' in res) {
                                paintValidationErrors(res.errors);
                            }
                            Swal.close();
                            return;
                        }

                        if (res.success) {
                            toastr.success(res.message, 'DATOS DE FACTURACIÓN ACTUALIZADOS');
                        } else {
                            toastr.error(res.message, 'ERROR EN EL SERVIDOR');
                        }

                    } catch (error) {
                        toastr.error(error, 'ERROR EN LA PETICIÓN ACTUALIZAR FACTURACIÓN DE LA EMPRESA');
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

        function changeDepartment(department_id) {

            const lstProvinces = @json($provinces);
            const lstDistricts = @json($districts);

            let lstProvincesFiltered = [];

            if (department_id) {

                departamento_id = String(department_id).padStart(2, '0');

                lstProvincesFiltered = lstProvinces.filter((province) => {
                    return province.department_id == department_id;
                })

                window.provinceSelect.clearOptions();

                lstProvincesFiltered.forEach(province => {
                    window.provinceSelect.addOption({
                        id: province.id,
                        description: province.name
                    });
                });

                window.provinceSelect.setValue(null);
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

                window.districtSelect.clearOptions();

                lstDistrictsFiltered.forEach(district => {
                    window.districtSelect.addOption({
                        description: district.name,
                        id: district.id
                    });
                });

                window.districtSelect.setValue(null);

            }

        }
    </script>
@endsection
