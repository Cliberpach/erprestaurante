@extends('layouts.template')

@section('title')
    Cuentas Cliente
@endsection

@push('js-head')
    @vite(['resources/js/libs/filepond.js'])
    @vite(['resources/js/libs/lightgalery.js'])
@endpush

@section('content')
    @include('accounts.customer_accounts.modals.mdl_pay')
    @include('accounts.customer_accounts.modals.mdl_historial')

    <div class="card">
        <div class="card-header">

            <div class="row align-items-center mb-3">
                <div class="col-12">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-file-invoice-dollar me-2 text-primary"></i>LISTA DE CUENTAS CLIENTE
                    </h4>
                </div>
            </div>

            {{-- ── Filtros ──────────────────────────────────────────────── --}}
            <div class="row g-2 align-items-end">

                {{-- Cliente --}}
                <div class="col-lg-4 col-md-6 col-12">
                    <label class="form-label fw-bold mb-1">
                        <i class="fas fa-user text-primary me-1"></i>Cliente
                    </label>
                    <select id="ca-customer-id" name="ca-customer-id" class="form-control">
                        <option value="">Seleccione un cliente</option>
                    </select>
                </div>

                {{-- Fecha inicio --}}
                <div class="col-lg-2 col-md-3 col-6">
                    <label class="form-label fw-bold mb-1">
                        <i class="fas fa-calendar-alt text-success me-1"></i>Fecha inicio
                    </label>
                    <input type="date" class="form-control" id="ca-start-date">
                </div>

                {{-- Fecha fin --}}
                <div class="col-lg-2 col-md-3 col-6">
                    <label class="form-label fw-bold mb-1">
                        <i class="fas fa-calendar-check text-danger me-1"></i>Fecha fin
                    </label>
                    <input type="date" class="form-control" id="ca-end-date">
                </div>

                {{-- Estado --}}
                <div class="col-lg-2 col-md-3 col-6">
                    <label class="form-label fw-bold mb-1">
                        <i class="fas fa-tasks text-info me-1"></i>Estado
                    </label>
                    <select class="form-control" id="ca-status">
                        <option value="">Todo</option>
                        <option value="PENDIENTE" selected>Pendiente</option>
                        <option value="PAGADO">Pagado</option>
                    </select>
                </div>

                {{-- Botones --}}
                <div class="col-lg-2 col-md-3 col-6 d-flex gap-1">
                    <button type="button" class="btn btn-primary flex-fill" onclick="filtrarCuentas()">
                        <i class="fas fa-filter me-1"></i>Filtrar
                    </button>
                    <button type="button" class="btn btn-outline-secondary flex-fill" onclick="limpiarFiltrosCuentas()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

            </div>

            {{-- ── Exportar ─────────────────────────────────────────────── --}}
            <div class="row mt-2">
                <div class="col-12 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-success btn-sm" onclick="downloadExcelCuentas()">
                        <i class="fas fa-file-excel me-1"></i>EXCEL
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="downloadPdfCuentas()">
                        <i class="fas fa-file-pdf me-1"></i>PDF
                    </button>
                </div>
            </div>

        </div>

        <div class="card-body">
            <div class="row">
                <div class="col">
                    <div class="table-responsive">
                        @include('accounts.customer_accounts.tables.tbl_list_cuentas')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('js')
    <script>
        let dtCuentasCliente = null;

        document.addEventListener('DOMContentLoaded', () => {
            iniciarDtCuentasCliente();
            iniciarFiltroCustomer();
            events();
            setDatosDefault();
        });

        function events() {
            eventsMdlPagar();
            eventsMdlHistory();
            iniciarSelectsMdlPagar();
        }

        // ── TomSelect filtro cliente ──────────────────────────────────────
        function iniciarFiltroCustomer() {
            window.caClienteSelect = new TomSelect('#ca-customer-id', {
                valueField:  'id',
                labelField:  'full_name',
                searchField: ['full_name'],
                plugins:     ['clear_button'],
                placeholder: 'Buscar cliente...',
                maxOptions:  20,
                create:      false,
                preload:     false,
                load: async (query, callback) => {
                    if (!query.length) return callback();
                    try {
                        const url = `{{ route('tenant.utils.searchCustomer') }}?q=${encodeURIComponent(query)}`;
                        const res = await fetch(url);
                        if (!res.ok) throw new Error();
                        const data = await res.json();
                        callback(data.data ?? []);
                    } catch {
                        callback();
                    }
                },
                render: {
                    option: (item, escape) => `<div><strong>${escape(item.full_name)}</strong></div>`,
                    item:   (item, escape) => `<div>${escape(item.full_name)}</div>`,
                },
            });
        }

        // ── Acciones de filtro ────────────────────────────────────────────
        function filtrarCuentas() {
            dtCuentasCliente.ajax.reload();
        }

        function limpiarFiltrosCuentas() {
            window.caClienteSelect.clear();
            document.querySelector('#ca-start-date').value = '';
            document.querySelector('#ca-end-date').value   = '';
            document.querySelector('#ca-status').value     = 'PENDIENTE';
            dtCuentasCliente.ajax.reload();
        }

        function downloadPdfCuentas() {
            const url = route('tenant.cuentas.cliente.pdfAll', {
                customer:   document.querySelector('#ca-customer-id').value,
                start_date: document.querySelector('#ca-start-date').value,
                end_date:   document.querySelector('#ca-end-date').value,
                status:     document.querySelector('#ca-status').value,
            });
            window.open(url, '_blank');
        }

        function downloadExcelCuentas() {
            const url = route('tenant.cuentas.cliente.excelAll', {
                customer:   document.querySelector('#ca-customer-id').value,
                start_date: document.querySelector('#ca-start-date').value,
                end_date:   document.querySelector('#ca-end-date').value,
                status:     document.querySelector('#ca-status').value,
            });
            window.open(url, '_blank');
        }

        // ── DataTable ─────────────────────────────────────────────────────
        function iniciarDtCuentasCliente() {
            dtCuentasCliente = $('.dataTables-cajas').DataTable({
                processing:    true,
                serverSide:    true,
                bPaginate:     true,
                bLengthChange: true,
                bFilter:       true,
                bInfo:         true,
                bAutoWidth:    false,
                ajax: {
                    url: "{{ route('tenant.cuentas.cliente.getCustomerAccounts') }}",
                    data: function (d) {
                        d.customer   = $('#ca-customer-id').val();
                        d.start_date = $('#ca-start-date').val();
                        d.end_date   = $('#ca-end-date').val();
                        d.status     = document.querySelector('#ca-status').value;
                    }
                },
                columns: [
                    { data: 'id',            name: 'ca.id',            visible: false },
                    { data: 'customer_info', name: 'customer_info' },
                    { data: 'document_serie',name: 'ca.document_serie' },
                    { data: 'document_date', name: 'ca.document_date',  searchable: false },
                    {
                        data: 'amount', name: 'ca.amount',
                        searchable: false, orderable: false, className: 'text-end',
                        render: (d) => formatSoles(d)
                    },
                    {
                        data: 'paid', name: 'ca.paid',
                        searchable: false, orderable: false,
                        className: 'text-end text-success fw-semibold',
                        render: (d) => formatSoles(d ?? 0)
                    },
                    {
                        data: 'balance', name: 'ca.balance',
                        searchable: false, orderable: false,
                        className: 'text-end text-danger fw-semibold',
                        render: (d) => formatSoles(d)
                    },
                    {
                        data: 'status', name: 'ca.status',
                        searchable: false, orderable: false, className: 'text-center',
                        render: function (data) {
                            const map = { PENDIENTE: 'bg-danger', PAGADO: 'bg-success', ANULADO: 'bg-dark' };
                            return `<span class="badge ${map[data] ?? 'bg-secondary'}">${data ?? ''}</span>`;
                        }
                    },
                    {
                        data: null, orderable: false, searchable: false, className: 'text-center',
                        render: function (data, type, row) {
                            return `
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-primary dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="#"
                                               onclick="openMdlPagar(${row.id}); return false;">
                                                <i class="fas fa-money-bill-wave me-2 text-success"></i>Pagar
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item" href="#"
                                               onclick="openMdlHistorial(${row.id}); return false;">
                                                <i class="fas fa-history me-2 text-primary"></i>Historial
                                            </a>
                                        </li>
                                    </ul>
                                </div>`;
                        }
                    }
                ],
                language: {
                    decimal:         '',
                    emptyTable:      'No hay datos disponibles en la tabla',
                    info:            'Mostrando _START_ a _END_ de _TOTAL_ registros',
                    infoEmpty:       'Mostrando 0 a 0 de 0 registros',
                    infoFiltered:    '(filtrado de _MAX_ registros totales)',
                    thousands:       ',',
                    lengthMenu:      'Mostrar _MENU_ registros',
                    loadingRecords:  'Cargando...',
                    processing:      'Procesando...',
                    search:          'Buscar:',
                    searchPlaceholder: 'Cliente o documento...',
                    zeroRecords:     'No se encontraron registros coincidentes',
                    paginate: { first: 'Primero', last: 'Último', next: 'Siguiente', previous: 'Anterior' },
                },
                order: [[0, 'desc']],
                drawCallback: function () {
                    document.querySelectorAll('.dataTables-cajas [data-bs-toggle="dropdown"]').forEach(function (el) {
                        const existing = bootstrap.Dropdown.getInstance(el);
                        if (existing) existing.dispose();
                        new bootstrap.Dropdown(el, { popperConfig: { strategy: 'fixed' } });
                    });
                },
                initComplete: function () {
                    $(this.api().table().container())
                        .find('.dt-search, .dataTables_filter')
                        .append('<div class="text-muted mt-1" style="font-size:.7rem;">' +
                            'Buscable por: <strong>Cliente</strong> y <strong>Documento</strong>' +
                        '</div>');
                }
            });
        }

        function setDatosDefault() {
            window.modoPagoSelect.setValue(3);
        }
    </script>
@endsection
