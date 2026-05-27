<div class="modal fade" id="modal_historial" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title fw-bold text-uppercase">
                    <i class="fas fa-history text-primary me-2"></i>Historial de Pagos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">

                {{-- ── Información de la cuenta ───────────────────────────── --}}
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header py-2">
                        <h6 class="fw-bold mb-0">
                            <i class="fas fa-info-circle text-info me-2"></i>Información de la Cuenta
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3" style="font-size:.82rem;">

                            <div class="col-md-6 col-12">
                                <span class="text-muted fw-bold">
                                    <i class="fas fa-user me-1"></i>Cliente
                                </span>
                                <p class="fw-semibold mb-0" id="hist-cliente"></p>
                            </div>

                            <div class="col-md-3 col-6">
                                <span class="text-muted fw-bold">
                                    <i class="fas fa-file-alt me-1"></i>Documento
                                </span>
                                <p class="mb-0" id="hist-documento"></p>
                            </div>

                            <div class="col-md-3 col-6">
                                <span class="text-muted fw-bold">
                                    <i class="fas fa-calendar-alt me-1"></i>Fecha
                                </span>
                                <p class="mb-0" id="hist-fecha"></p>
                            </div>

                            <div class="col-md-3 col-6">
                                <span class="text-muted fw-bold">
                                    <i class="fas fa-money-bill-wave text-success me-1"></i>Monto Total
                                </span>
                                <p class="fw-bold text-success mb-0" id="hist-monto"></p>
                            </div>

                            <div class="col-md-3 col-6">
                                <span class="text-muted fw-bold">
                                    <i class="fas fa-check-circle text-primary me-1"></i>Pagado
                                </span>
                                <p class="mb-0 fw-bold text-primary" id="hist-acuerdo"></p>
                            </div>

                            <div class="col-md-3 col-6">
                                <span class="text-muted fw-bold">
                                    <i class="fas fa-balance-scale text-warning me-1"></i>Saldo
                                </span>
                                <p class="fw-bold text-danger mb-0" id="hist-saldo"></p>
                            </div>

                            <div class="col-md-3 col-6">
                                <span class="text-muted fw-bold">
                                    <i class="fas fa-circle me-1"></i>Estado
                                </span>
                                <p class="mb-0" id="hist-estado"></p>
                            </div>

                            <div class="col-12 d-flex justify-content-end">
                                <a id="hist-btn-pdf" href="#" target="_blank"
                                    class="btn btn-danger btn-sm d-flex align-items-center gap-2 shadow-sm">
                                    <i class="fas fa-file-pdf"></i> Ver PDF
                                </a>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ── Tabla de pagos ─────────────────────────────────────── --}}
                <div class="table-responsive" id="hist-table-container">
                    <table id="tbl_historial_pagos" class="table-sm table-hover table-bordered text-uppercase table">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Fecha</th>
                                <th class="text-center">Observación</th>
                                <th class="text-center">Efectivo</th>
                                <th class="text-center">Importe</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Saldo Tras Pago</th>
                                <th class="text-center">Imagen</th>
                            </tr>
                        </thead>
                        <tbody id="hist-tbody"></tbody>
                    </table>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cerrar
                </button>
            </div>

        </div>
    </div>
</div>

<script>
    const paramsMdlHistorial = {
        id: null,
        dt: null,
        lg: null,
    };

    function eventsMdlHistory() {
        document.getElementById('modal_historial').addEventListener('hidden.bs.modal', function () {
            if (paramsMdlHistorial.dt) { paramsMdlHistorial.dt.destroy(); paramsMdlHistorial.dt = null; }
            if (paramsMdlHistorial.lg) { paramsMdlHistorial.lg.destroy(); paramsMdlHistorial.lg = null; }
            document.querySelector('#hist-tbody').innerHTML = '';
        });
    }

    // ─── Abrir modal historial ────────────────────────────────────────────────
    async function openMdlHistorial(cuentaId) {
        paramsMdlHistorial.id = cuentaId;

        mostrarAnimacion1();
        const data = await getCuentaCliente(cuentaId);
        if (!data) {
            ocultarAnimacion1();
            return;
        }

        pintarInfoHistorial(data.cuenta);
        cargarTablaHistorial(data.detalle);

        bootstrap.Modal.getOrCreateInstance(document.getElementById('modal_historial')).show();
        ocultarAnimacion1();
    }

    // ─── Pintar sección informativa ───────────────────────────────────────────
    function pintarInfoHistorial(cuenta) {
        document.querySelector('#hist-cliente').textContent    = cuenta.customer_info ?? '—';
        document.querySelector('#hist-documento').textContent  = cuenta.document_serie ?? '—';
        document.querySelector('#hist-fecha').textContent      = cuenta.document_date ?? '—';
        document.querySelector('#hist-monto').textContent      = formatSoles(cuenta.amount);
        document.querySelector('#hist-acuerdo').textContent    = formatSoles(cuenta.paid ?? 0);
        document.querySelector('#hist-saldo').textContent      = formatSoles(cuenta.balance);

        const estadoEl = document.querySelector('#hist-estado');
        const badgeMap = { PAGADO: 'bg-success', PENDIENTE: 'bg-danger', ANULADO: 'bg-dark' };
        const cls = badgeMap[cuenta.status] ?? 'bg-secondary';
        estadoEl.innerHTML = `<span class="badge ${cls}">${cuenta.status ?? '—'}</span>`;

        document.querySelector('#hist-btn-pdf').href = route('tenant.cuentas.cliente.pdfOne', {
            id: paramsMdlHistorial.id
        });
    }

    // ─── Cargar tabla + LightGallery ─────────────────────────────────────────
    function cargarTablaHistorial(detalle) {
        if (paramsMdlHistorial.dt) { paramsMdlHistorial.dt.destroy(); paramsMdlHistorial.dt = null; }
        if (paramsMdlHistorial.lg) { paramsMdlHistorial.lg.destroy(); paramsMdlHistorial.lg = null; }

        const BASE = @json(asset(''));
        const tbody = document.querySelector('#hist-tbody');
        tbody.innerHTML = '';

        detalle.forEach((row, idx) => {
            const tr = document.createElement('tr');
            let imgCell = '<span class="text-muted">—</span>';

            if (row.img_route) {
                const fullUrl = `${BASE}${row.img_route}`;
                imgCell = `
                    <a href="${fullUrl}" class="lg-item" style="display:inline-block;cursor:pointer;"
                       data-sub-html="<h5>Pago #${idx + 1} — ${row.date ?? ''}</h5><p>${row.observation ?? ''}</p>">
                        <img src="${fullUrl}"
                             style="width:56px;height:56px;object-fit:cover;border-radius:6px;border:1px solid #dee2e6;"
                             loading="lazy" />
                    </a>`;
            }

            tr.innerHTML = `
                <td class="text-center">${idx + 1}</td>
                <td class="text-center">${row.date ?? '—'}</td>
                <td>${row.observation ?? '—'}</td>
                <td class="text-center">${formatSoles(row.cash ?? 0)}</td>
                <td class="text-center">${formatSoles(row.amount ?? 0)}</td>
                <td class="text-center fw-bold">${formatSoles(row.total ?? 0)}</td>
                <td class="text-center">${formatSoles(row.balance ?? 0)}</td>
                <td class="text-center">${imgCell}</td>
            `;
            tbody.appendChild(tr);
        });

        paramsMdlHistorial.dt = new DataTable('#tbl_historial_pagos', {
            paging:       true,
            pageLength:   10,
            lengthChange: false,
            searching:    false,
            info:         true,
            ordering:     false,
            language: {
                emptyTable: 'Sin pagos registrados',
                info:       'Mostrando _START_ a _END_ de _TOTAL_ pagos',
                infoEmpty:  'Sin pagos',
                paginate:   { previous: 'Anterior', next: 'Siguiente' },
            },
        });

        if (detalle.some(r => r.img_route)) {
            paramsMdlHistorial.lg = lightGallery(document.querySelector('#hist-table-container'), {
                selector: '.lg-item',
                plugins:  [lgZoom, lgThumbnail],
                mobileSettings: { controls: true, showCloseIcon: true },
            });
        }
    }
</script>
