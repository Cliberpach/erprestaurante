@extends('layouts.template')

@section('title')
    Egresos
@endsection

@section('content')
    <div class="card overflow-hidden">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="card-title mb-0">LISTA DE EGRESOS</h6>
            <div class="input-group-append">
                <a href="{{ route('tenant.cajas.egresos.create') }}" class="btn btn-primary">
                    <div class="lign-items-center d-flex align-items-center">
                        <i class="fas fa-plus pe-1"></i>
                        <p class="mb-0 ml-2">NUEVO</p>
                    </div>
                </a>
            </div>
        </div>
        <div class="card-body p-0 pb-2">
            <div class="row g-2 align-items-end px-3 pt-3 pb-2">

                {{-- Fecha inicio --}}
                <div class="col-lg-2 col-md-3 col-6">
                    <label class="form-label fw-bold mb-1">
                        <i class="fas fa-calendar-alt text-success me-1"></i>Desde
                    </label>
                    <input type="date" id="date_start" class="form-control" value="{{ now()->toDateString() }}">
                </div>

                {{-- Fecha fin --}}
                <div class="col-lg-2 col-md-3 col-6">
                    <label class="form-label fw-bold mb-1">
                        <i class="fas fa-calendar-check text-danger me-1"></i>Hasta
                    </label>
                    <input type="date" id="date_end" class="form-control" value="{{ now()->toDateString() }}">
                </div>

                {{-- Centro de costos --}}
                <div class="col-lg-3 col-md-4 col-12">
                    <label class="form-label fw-bold mb-1">
                        <i class="fas fa-tags text-warning me-1"></i>Centro de Costos
                    </label>
                    <select id="filter_cost_center" class="form-control">
                        <option value="">Todos</option>
                        @foreach($cost_centers as $cc)
                            <option value="{{ $cc->id }}">{{ $cc->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Proveedor --}}
                <div class="col-lg-3 col-md-4 col-12">
                    <label class="form-label fw-bold mb-1">
                        <i class="fas fa-truck text-primary me-1"></i>Proveedor
                    </label>
                    <select id="filter_supplier" class="form-control">
                        <option value="">Todos</option>
                    </select>
                </div>

                {{-- Botón filtrar --}}
                <div class="col-lg-2 col-md-2 col-12">
                    <button type="button" class="btn btn-primary w-100 btnFilter">
                        <i class="fas fa-filter me-1"></i>Filtrar
                    </button>
                </div>

            </div>

            <div class="row">
                <div class="col-12 text-end">
                    <button class="btn btn-success me-2" onclick="downloadExcel();">
                        <i class="fas fa-file-excel me-1"></i> Excel
                    </button>

                    <button class="btn btn-danger" onclick="downloadPdf();">
                        <i class="fas fa-file-pdf me-1"></i> PDF
                    </button>
                </div>
                <div class="col-12">
                    <div class="table-responsive">
                        @include('cash.exit-money.tables.tbl_list_exit_money')
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('js')
    <script>
        let dtExitMoneys = null;

        document.addEventListener('DOMContentLoaded', () => {
            iniciarSelectsFiltros();
            iniciarDtExitMoneys();
            events();
        });

        function events() {
            document.addEventListener('click', (e) => {
                if (e.target.closest('.btnFilter')) filtrar();
            });
        }

        function iniciarSelectsFiltros() {
            // Centro de costos — no server-side (opciones ya están en el HTML)
            window.filterCostCenterSelect = new TomSelect('#filter_cost_center', {
                placeholder: 'Todos los centros',
                plugins:     ['clear_button'],
                create:      false,
            });

            // Proveedor — server-side
            window.filterSupplierSelect = new TomSelect('#filter_supplier', {
                valueField:  'id',
                labelField:  'full_name',
                searchField: ['full_name'],
                plugins:     ['clear_button'],
                placeholder: 'Buscar proveedor...',
                maxOptions:  20,
                create:      false,
                preload:     false,
                load: async (query, callback) => {
                    if (!query.length) return callback();
                    try {
                        const url = `{{ route('tenant.utils.searchSupplier') }}?q=${encodeURIComponent(query)}`;
                        const res = await fetch(url);
                        if (!res.ok) throw new Error();
                        const data = await res.json();
                        callback(data.data ?? []);
                    } catch { callback(); }
                },
                render: {
                    option: (item, escape) => `<div><strong>${escape(item.full_name)}</strong></div>`,
                    item:   (item, escape) => `<div>${escape(item.full_name)}</div>`,
                },
            });
        }

        function iniciarDtExitMoneys() {
            const url = '{{ route('tenant.cajas.egresos.getExitMoneys') }}';

            dtExitMoneys = new DataTable('#dt-exit-moneys', {
                processing: true,
                ajax: {
                    url: url,
                    type: 'GET',
                    data: function(d) {
                        d.date_start      = document.querySelector('#date_start').value;
                        d.date_end        = document.querySelector('#date_end').value;
                        d.cost_center_id  = document.querySelector('#filter_cost_center').value;
                        d.supplier_id     = document.querySelector('#filter_supplier').value;
                    }
                },
                columns: [{
                        data: 'id',
                        className: "text-center",
                        visible: false,
                        searchable: false
                    },
                    {
                        data: 'cash_book_code',
                        name: 'cash_book_code',
                        className: "text-center",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'date',
                        name: 'em.date',
                        className: "text-center",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'cost_center_name',
                        name: 'em.cost_center_name',
                        className: "text-center",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'supplier_name',
                        name: 's.name',
                        className: "text-center",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'number',
                        name: 'em.number',
                        className: "text-center",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'payment_method_name',
                        name: 'em.payment_method_name',
                        className: "text-center",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'total',
                        name: 'em.total',
                        className: "text-center",
                        orderable: true,
                        searchable: false,
                        render: function(data) {
                            return formatSoles(data);
                        }
                    },
                    {
                        data: 'discount_cash',
                        name: 'em.discount_cash',
                        className: "text-center align-middle",
                        orderable: true,
                        searchable: false,
                        render: function(data) {

                            if (data == 1 || data === true) {
                                return `
                                    <span class="badge rounded-pill bg-primary-subtle text-primary fw-semibold px-2 py-1">
                                        <i class="fas fa-cash-register me-1"></i>
                                        SI
                                    </span>
                                `;
                            } else {
                                return `
                                    <span class="badge rounded-pill bg-danger-subtle text-danger fw-semibold px-2 py-1">
                                        <i class="fas fa-ban me-1"></i>
                                        NO
                                    </span>
                                `;
                            }
                        }
                    },
                    {
                        data: 'first_item',
                        name: 'em.first_item',
                        className: "text-center",
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data;
                        }
                    },
                    {
                        data: null,
                        searchable: false,
                        className: "text-center",
                        render: function(data) {

                            const pdfUrl = `{{ route('tenant.cajas.egresos.pdf', ':id') }}`
                                .replace(':id', data.id);

                            const editUrl = `{{ route('tenant.cajas.egresos.edit', ':id') }}`
                                .replace(':id', data.id);

                            return `
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-primary border dropdown-toggle"
                                            type="button"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">

                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2"
                                            href="${pdfUrl}" target="_blank">
                                                <i class="fas fa-file-pdf text-danger"></i>
                                                PDF
                                            </a>
                                        </li>

                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2"
                                            href="${editUrl}">
                                                <i class="fas fa-pen text-primary"></i>
                                                Editar
                                            </a>
                                        </li>

                                        <li><hr class="dropdown-divider"></li>

                                        <li>
                                            <button class="dropdown-item d-flex align-items-center gap-2 text-danger"
                                                    onclick="anularExitMoney(${data.id})">
                                                <i class="fas fa-ban"></i>
                                                Anular
                                            </button>
                                        </li>

                                    </ul>
                                </div>
                            `;
                        }
                    }
                ],
                language: {
                    decimal: "",
                    emptyTable: "No hay datos disponibles en la tabla",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    infoEmpty: "Mostrando 0 a 0 de 0 registros",
                    infoFiltered: "(filtrado de _MAX_ registros totales)",
                    lengthMenu: "Mostrar _MENU_ registros",
                    loadingRecords: "Cargando...",
                    processing: "Procesando...",
                    search: "Buscar:",
                    zeroRecords: "No se encontraron registros coincidentes",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior"
                    },
                },

                order: [
                    [0, "desc"]
                ],
                initComplete: function () {
                    $(this.api().table().container())
                        .find('.dt-search, .dataTables_filter')
                        .append('<small class="text-muted d-block mt-1" style="font-size:.7rem;">' +
                            'Busca por: <strong>Caja</strong>, <strong>Fecha</strong>, <strong>Centro Costos</strong>, <strong>Proveedor</strong>, <strong>N° Doc</strong>, <strong>Método Pago</strong>' +
                        '</small>');
                }
            });
        }

        function anularExitMoney(id) {
            const fila = getRowById(dtExitMoneys, id);
            const costCenterName = fila.cost_center_name;
            const total = parseFloat(fila.total).toFixed(2);
            const date = fila.date;
            const supplier = fila.supplier_name;

            const messageHtml = `
                <div class="text-center" style="font-size: 15px;">
                    <p class="mb-2">
                        <i class="fas fa-calendar-alt text-primary me-2"></i>
                        <strong>Fecha:</strong> ${date}
                    </p>
                    <p class="mb-2">
                        <i class="fas fa-user text-primary me-2"></i>
                        <strong>Proveedor:</strong> ${supplier}
                    </p>
                    <p class="mb-2">
                        <i class="fas fa-receipt text-primary me-2"></i>
                        <strong>Motivo:</strong> ${costCenterName}
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-dollar-sign text-success me-2"></i>
                        <strong>Total:</strong> S/ ${total}
                    </p>
                </div>
            `;

            Swal.fire({
                title: '¿Desea anular este egreso?',
                html: messageHtml,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, anular',
                cancelButtonText: 'No, cancelar',
                focusCancel: true,
                reverseButtons: true
            }).then(async (result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Anulando egreso...',
                        html: `
                    <div style="display:flex; align-items:center; justify-content:center; flex-direction:column;">
                        <i class="fa fa-spinner fa-spin fa-3x text-primary mb-3"></i>
                        <p style="margin:0; font-weight:600;">Por favor, espere un momento</p>
                    </div>
                `,
                        allowOutsideClick: false,
                        showConfirmButton: false
                    });

                    try {
                        const res = await axios.delete(route('tenant.cajas.egresos.destroy', id), {
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        if (res.data.success) {
                            toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                            dtExitMoneys.ajax.reload();
                        } else {
                            toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                        }

                    } catch (error) {
                        toastr.error(error.response?.data?.message || error.message, 'ERROR EN LA PETICIÓN');
                    } finally {
                        Swal.close();
                    }

                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: 'Cancelado',
                        text: 'La solicitud ha sido cancelada.',
                        icon: 'error',
                        confirmButtonText: 'Entendido',
                        customClass: {
                            confirmButton: 'btn btn-secondary'
                        },
                        buttonsStyling: false
                    });
                }
            });
        }

        function getFilterParams() {
            return new URLSearchParams({
                date_start:     document.querySelector('#date_start').value,
                date_end:       document.querySelector('#date_end').value,
                cost_center_id: document.querySelector('#filter_cost_center').value,
                supplier_id:    document.querySelector('#filter_supplier').value,
            }).toString();
        }

        function downloadExcel() {
            window.location.href = `@json(route('tenant.cajas.egresos.excelAll'))?${getFilterParams()}`;
        }

        function downloadPdf() {
            window.open(`@json(route('tenant.cajas.egresos.pdfAll'))?${getFilterParams()}`, '_blank');
        }


        function filtrar() {
            toastr.clear();
            const fecha_inicio = document.querySelector('#date_start').value;
            const fecha_fin = document.querySelector('#date_end').value;

            if (fecha_inicio > fecha_fin && fecha_fin && fecha_inicio) {
                toastr.error('LA FECHA DE INICIO DEBE SER MENOR IGUAL A LA FECHA FINAL!!');
                document.querySelector('#fecha_inicio').focus();
                return;
            }
            dtExitMoneys.ajax.reload();
        }
    </script>
@endsection
