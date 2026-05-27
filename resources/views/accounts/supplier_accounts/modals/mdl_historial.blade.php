<div class="modal fade" id="modal_historial_prov" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title fw-bold text-uppercase">
                    <i class="fas fa-history text-primary me-2"></i>Historial de Pagos — Proveedor
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
                                    <i class="fas fa-user-tie me-1"></i>Proveedor
                                </span>
                                <p class="fw-semibold mb-0" id="hist-prov-proveedor"></p>
                            </div>

                            <div class="col-md-3 col-6">
                                <span class="text-muted fw-bold">
                                    <i class="fas fa-file-alt me-1"></i>Documento
                                </span>
                                <p class="mb-0" id="hist-prov-documento"></p>
                            </div>

                            <div class="col-md-3 col-6">
                                <span class="text-muted fw-bold">
                                    <i class="fas fa-calendar-alt me-1"></i>Fecha
                                </span>
                                <p class="mb-0" id="hist-prov-fecha"></p>
                            </div>

                            <div class="col-md-3 col-6">
                                <span class="text-muted fw-bold">
                                    <i class="fas fa-money-bill-wave text-success me-1"></i>Monto Total
                                </span>
                                <p class="fw-bold text-success mb-0" id="hist-prov-monto"></p>
                            </div>

                            <div class="col-md-3 col-6">
                                <span class="text-muted fw-bold">
                                    <i class="fas fa-check-circle text-primary me-1"></i>Pagado
                                </span>
                                <p class="fw-bold text-primary mb-0" id="hist-prov-pagado"></p>
                            </div>

                            <div class="col-md-3 col-6">
                                <span class="text-muted fw-bold">
                                    <i class="fas fa-balance-scale text-warning me-1"></i>Saldo
                                </span>
                                <p class="fw-bold text-danger mb-0" id="hist-prov-saldo"></p>
                            </div>

                            <div class="col-md-3 col-6">
                                <span class="text-muted fw-bold">
                                    <i class="fas fa-circle me-1"></i>Estado
                                </span>
                                <p class="mb-0" id="hist-prov-estado"></p>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ── Tabla de pagos ─────────────────────────────────────── --}}
                <div class="table-responsive" id="hist-prov-table-container">
                    <table id="tbl_historial_prov" class="table table-sm table-hover table-bordered text-uppercase">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Fecha</th>
                                <th class="text-center">Observación</th>
                                <th class="text-center">Caja</th>
                                <th class="text-center">Método Pago</th>
                                <th class="text-center">Efectivo</th>
                                <th class="text-center">Importe</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Saldo Tras Pago</th>
                                <th class="text-center">Imagen</th>
                            </tr>
                        </thead>
                        <tbody id="hist-prov-tbody"></tbody>
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
    const paramsMdlHistorialProv = {
        id: null,
        dt: null,
        lg: null,
    };

    function eventsMdlHistorialProv() {
        document.getElementById('modal_historial_prov').addEventListener('hidden.bs.modal', function () {
            if (paramsMdlHistorialProv.dt) { paramsMdlHistorialProv.dt.destroy(); paramsMdlHistorialProv.dt = null; }
            if (paramsMdlHistorialProv.lg) { paramsMdlHistorialProv.lg.destroy(); paramsMdlHistorialProv.lg = null; }
            document.querySelector('#hist-prov-tbody').innerHTML = '';
        });
    }

    async function openMdlHistorialProv(cuentaId) {
        paramsMdlHistorialProv.id = cuentaId;

        mostrarAnimacion1();
        const res = await getCobranza(cuentaId);
        if (!res || !res.success) {
            ocultarAnimacion1();
            toastr.error(res?.message ?? 'Error al obtener la cuenta', 'ERROR');
            return;
        }

        pintarInfoHistorialProv(res.data.cuenta);
        cargarTablaHistorialProv(res.data.detalle);

        bootstrap.Modal.getOrCreateInstance(document.getElementById('modal_historial_prov')).show();
        ocultarAnimacion1();
    }

    function pintarInfoHistorialProv(cuenta) {
        document.querySelector('#hist-prov-proveedor').textContent = cuenta.supplier_name  ?? '—';
        document.querySelector('#hist-prov-documento').textContent = cuenta.document_number ?? '—';
        document.querySelector('#hist-prov-fecha').textContent     = cuenta.document_date  ?? cuenta.created_at ?? '—';
        document.querySelector('#hist-prov-monto').textContent     = formatSoles(cuenta.amount);
        document.querySelector('#hist-prov-pagado').textContent    = formatSoles(cuenta.paid ?? 0);
        document.querySelector('#hist-prov-saldo').textContent     = formatSoles(cuenta.balance);

        const estadoEl = document.querySelector('#hist-prov-estado');
        const badgeMap = { PAGADO: 'bg-success', PENDIENTE: 'bg-danger', ANULADO: 'bg-dark' };
        estadoEl.innerHTML = `<span class="badge ${badgeMap[cuenta.status] ?? 'bg-secondary'}">${cuenta.status ?? '—'}</span>`;
    }

    function cargarTablaHistorialProv(detalle) {
        if (paramsMdlHistorialProv.dt) { paramsMdlHistorialProv.dt.destroy(); paramsMdlHistorialProv.dt = null; }
        if (paramsMdlHistorialProv.lg) { paramsMdlHistorialProv.lg.destroy(); paramsMdlHistorialProv.lg = null; }

        const BASE = @json(asset(''));
        const tbody = document.querySelector('#hist-prov-tbody');
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
                <td class="text-center">${row.petty_cash_name ?? '—'}</td>
                <td class="text-center">${row.payment_method_name ?? '—'}</td>
                <td class="text-center">${formatSoles(row.cash ?? 0)}</td>
                <td class="text-center">${formatSoles(row.amount ?? 0)}</td>
                <td class="text-center fw-bold">${formatSoles(row.total ?? 0)}</td>
                <td class="text-center">${formatSoles(row.balance ?? 0)}</td>
                <td class="text-center">${imgCell}</td>
            `;
            tbody.appendChild(tr);
        });

        paramsMdlHistorialProv.dt = new DataTable('#tbl_historial_prov', {
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
            paramsMdlHistorialProv.lg = lightGallery(document.querySelector('#hist-prov-table-container'), {
                selector: '.lg-item',
                plugins:  [lgZoom, lgThumbnail],
                mobileSettings: { controls: true, showCloseIcon: true },
            });
        }
    }
</script>
