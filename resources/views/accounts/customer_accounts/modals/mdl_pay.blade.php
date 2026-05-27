<div class="modal fade" id="modal_detalle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title text-uppercase fw-bold">
                    <i class="fas fa-file-invoice-dollar me-2"></i>Detalle de Cuenta Cliente
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="cuenta_cliente_id" id="cuenta_cliente_id">
                @include('accounts.customer_accounts.forms.form_pagar')
            </div>

            <div class="modal-footer">
                <div class="col-md-6 text-end">
                    <button type="submit" class="btn btn-primary btn-sm" id="btn_guardar_detalle" form="frmDetalle">
                        <i class="fas fa-save me-1"></i>Guardar
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cerrar
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>


<script>
    const paramsMdlPay = {
        id: null,
        fpImg: null,
    };

    // ─── FilePond ────────────────────────────────────────────────────────────
    function loadFilePond() {
        const input = document.querySelector('#imagen');
        if (!input) return;

        // Destruir instancia previa si existe
        const existing = FilePond.find(input);
        if (existing) {
            existing.destroy();
        }

        paramsMdlPay.fpImg = FilePond.create(input, {
            allowImagePreview: true,
            imagePreviewHeight: 120,
            imageCropAspectRatio: '1:1',
            styleLayout: 'compact',
            stylePanelAspectRatio: 0.5,
            storeAsFile: true,
            acceptedFileTypes: ['image/*'],
            labelFileTypeNotAllowed: 'Solo se permiten imágenes.',
        });
    }

    // ─── Eventos del modal ───────────────────────────────────────────────────
    function eventsMdlPagar() {
        loadDtDetailsPay();

        document.querySelector('#frmDetalle').addEventListener('submit', (e) => {
            e.preventDefault();
            storePago(e.target);
        });

        // FilePond se inicializa cuando el modal está visible (evita problemas de DOM oculto)
        $('#modal_detalle').on('shown.bs.modal', function () {
            loadFilePond();
        });

        $('#modal_detalle').on('hidden.bs.modal', function () {
            limpiarMdlPagar();
        });
    }

    // ─── Abrir modal ─────────────────────────────────────────────────────────
    async function openMdlPagar(cuentaId) {
        paramsMdlPay.id = cuentaId;

        mostrarAnimacion1();
        const data = await getCuentaCliente(cuentaId);
        if (!data) { ocultarAnimacion1(); return; }

        pintarCuentaCliente(data.cuenta);
        pintarDetallePago(data.detalle);

        const urlPdfOne = route('tenant.cuentas.cliente.pdfOne', { id: cuentaId });
        $('#btn-detalle').attr('href', urlPdfOne);

        // Cargar cuentas del método de pago actual antes de mostrar
        const modoPagoId = document.querySelector('#modo_pago').value;
        if (modoPagoId) {
            const cuentas = await getCuentasPorMetodoPago(modoPagoId);
            if (cuentas) pintarCuentas(cuentas);
        }

        $('#modal_detalle').modal('show');
        ocultarAnimacion1();
    }

    // ─── DataTable historial ─────────────────────────────────────────────────
    function loadDtDetailsPay() {
        paramsMdlPay.dtDetailPay = new DataTable('.dataTables-detalle', {
            bPaginate: false,
            bLengthChange: true,
            bFilter: true,
            bInfo: false,
            bAutoWidth: false,
            processing: true,
            order: [[0, 'desc']],
            aoColumns: [
                { sClass: 'text-center' },
                { sClass: 'text-center' },
                { sClass: 'text-center' },
                { sClass: 'text-center' },
            ],
        });
    }

    // ─── API: obtener cuenta ─────────────────────────────────────────────────
    async function getCuentaCliente(cuentaId) {
        try {
            const res = await axios.get(route('tenant.cuentas.cliente.getCustomerAccount', { id: cuentaId }));
            if (!res.data.success) {
                toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                return null;
            }
            return res.data.data;
        } catch (error) {
            toastr.error(error, 'ERROR EN LA PETICIÓN OBTENER CUENTA CLIENTE');
            return null;
        }
    }

    // ─── Pintar datos de la cuenta ───────────────────────────────────────────
    function pintarCuentaCliente(cuenta) {
        document.querySelector('#cliente').textContent      = cuenta.customer_name ?? '—';
        document.querySelector('#numero').textContent       = cuenta.document_serie ?? '—';
        document.querySelector('#monto').textContent        = formatSoles(cuenta.amount);
        document.querySelector('#saldo').textContent        = formatSoles(cuenta.balance);
        document.querySelector('#estado').textContent       = cuenta.status ?? '—';

        // Pedido: tipo + serie
        const tipo  = cuenta.type_instance ?? '';
        const serie = cuenta.instance_serie ?? '—';
        document.querySelector('#instance_info').textContent = tipo ? `${tipo} - ${serie}` : serie;

        // Guardar balance numérico para cálculos
        document.querySelector('#balance_raw').value = cuenta.balance ?? 0;
    }

    // ─── Pintar historial de pagos ───────────────────────────────────────────
    function pintarDetallePago(detalle) {
        const table = $('.dataTables-detalle').DataTable();
        table.clear().draw();

        const BASE_STORAGE_URL = @json(asset(''));

        detalle.forEach((value) => {
            let imagenHTML = '—';
            if (value.img_route) {
                const link_img = `${BASE_STORAGE_URL}${value.img_route}`;
                imagenHTML = `<a href="${link_img}" target="_blank">
                    <img src="${link_img}" style="width:80px;height:80px;object-fit:contain;border-radius:4px;border:1px solid #ddd;" />
                </a>`;
            }
            table.row.add([
                value.date,
                value.observation,
                formatSoles(value.total),
                imagenHTML,
            ]).draw(false);
        });
    }

    // ─── Store pago ──────────────────────────────────────────────────────────
    function storePago(formPagar) {
        clearValidationErrors('msgError');

        // Validación manual: cuenta y nro_operacion requeridos si método != efectivo
        const modoPagoId = document.querySelector('#modo_pago').value;
        const esEfectivo = modoPagoId == 1;

        if (!esEfectivo) {
            const cuenta       = document.querySelector('#cuenta').value;
            const nroOperacion = document.querySelector('#nro_operacion').value.trim();
            let hayError = false;

            if (!cuenta) {
                document.querySelector('.cuenta_error').textContent = 'La cuenta es obligatoria para este método de pago.';
                hayError = true;
            }
            if (!nroOperacion) {
                document.querySelector('.nro_operacion_error').textContent = 'El N° de operación es obligatorio para este método de pago.';
                hayError = true;
            }
            if (hayError) {
                toastr.warning('Completa los campos obligatorios.', 'ATENCIÓN');
                return;
            }
        }

        Swal.fire({
            title: '¿Desea registrar el pago?',
            html: 'Confirmar',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí!',
            cancelButtonText: 'No, cancelar!',
            reverseButtons: true,
        }).then(async (result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Registrando pago...',
                    text: 'Por favor, espere',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => Swal.showLoading(),
                });

                try {
                    toastr.clear();
                    const formData = new FormData(formPagar);
                    formData.append('id', paramsMdlPay.id);
                    const res = await axios.post(route('tenant.cuentas.cliente.storePago'), formData);

                    if (res.data.success) {
                        toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                        $('#modal_detalle').modal('hide');
                        dtCuentasCliente.ajax.reload();
                    } else {
                        Swal.close();
                        toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                    }
                } catch (error) {
                    Swal.close();
                    if (error.response?.status === 422) {
                        paintValidationErrors(error.response.data.errors, 'error');
                        toastr.error('Errores de validación encontrados.', 'ERROR DE VALIDACIÓN');
                    } else if (error.response) {
                        toastr.error(error.response.data.message, 'ERROR EN EL SERVIDOR');
                    } else if (error.request) {
                        toastr.error('No se pudo contactar al servidor.', 'ERROR DE CONEXIÓN');
                    } else {
                        toastr.error(error.message, 'ERROR DESCONOCIDO');
                    }
                } finally {
                    Swal.close();
                }

            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire({ title: 'Operación cancelada', text: 'No se realizaron acciones', icon: 'error' });
            }
        });
    }

    // ─── Limpiar imagen ──────────────────────────────────────────────────────
    function limpiarImagen() {
        if (paramsMdlPay.fpImg) {
            paramsMdlPay.fpImg.removeFiles();
        }
    }

    // ─── Cambio método de pago ───────────────────────────────────────────────
    async function changeModoPago(b) {
        const esEfectivo = b.value == 1;

        if (esEfectivo) {
            document.querySelector('#efectivo_venta').removeAttribute('readonly');
            document.querySelector('#importe_venta').setAttribute('readonly', true);
            document.querySelector('#importe_venta').value = '0.00';
            changeEfectivo();
        } else {
            document.querySelector('#efectivo_venta').setAttribute('readonly', true);
            document.querySelector('#efectivo_venta').value = '0.00';
            document.querySelector('#importe_venta').removeAttribute('readonly');
        }

        // Mostrar/ocultar asteriscos de requerido
        document.querySelector('#lbl-cuenta-req').style.display  = esEfectivo ? 'none' : '';
        document.querySelector('#lbl-nroope-req').style.display  = esEfectivo ? 'none' : '';

        mostrarAnimacion1();
        toastr.clear();
        const cuentas = await getCuentasPorMetodoPago(b.value);
        if (cuentas) pintarCuentas(cuentas);
        ocultarAnimacion1();
    }

    // ─── Pintar cuentas en TomSelect ─────────────────────────────────────────
    function pintarCuentas(cuentas) {
        window.cuentaSelect.clear();
        window.cuentaSelect.clearOptions();
        window.cuentaSelect.addOptions(cuentas);
        window.cuentaSelect.refreshOptions(false);
    }

    // ─── Recalcular monto ────────────────────────────────────────────────────
    function changeEfectivo() {
        const efectivo = parseFloat(document.querySelector('#efectivo_venta').value) || 0;
        const importe  = parseFloat(document.querySelector('#importe_venta').value)  || 0;
        document.querySelector('#cantidad').value = (efectivo + importe).toFixed(2);
    }

    function changeImporte() {
        changeEfectivo();
    }

    // ─── Tipo de pago (TODO / A CUENTA) ──────────────────────────────────────
    function tipoPago(select) {
        const tipo        = select.value;
        const balanceRaw  = parseFloat(document.querySelector('#balance_raw').value) || 0;
        const modoPagoId  = document.querySelector('#modo_pago').value;
        const esEfectivo  = modoPagoId == 1;

        if (tipo === 'TODO') {
            if (esEfectivo) {
                document.querySelector('#efectivo_venta').value = balanceRaw.toFixed(2);
                document.querySelector('#importe_venta').value  = '0.00';
            } else {
                document.querySelector('#importe_venta').value  = balanceRaw.toFixed(2);
                document.querySelector('#efectivo_venta').value = '0.00';
            }
        } else {
            document.querySelector('#efectivo_venta').value = '0.00';
            document.querySelector('#importe_venta').value  = '0.00';
        }
        changeEfectivo();
    }

    // ─── Inicializar TomSelects ───────────────────────────────────────────────
    function iniciarSelectsMdlPagar() {
        window.pagoSelect = new TomSelect('#pago', {
            placeholder: 'SELECCIONAR',
            allowEmptyOption: false,
            create: false,
            maxOptions: null,
        });

        window.modoPagoSelect = new TomSelect('#modo_pago', {
            placeholder: 'SELECCIONAR',
            allowEmptyOption: false,
            create: false,
            maxOptions: null,
            onChange: function(value) {
                const select = document.querySelector('#modo_pago');
                select.value = value;
                changeModoPago(select);
            },
        });

        window.cuentaSelect = new TomSelect('#cuenta', {
            placeholder: 'SELECCIONAR',
            allowEmptyOption: true,
            create: false,
            maxOptions: null,
            valueField: 'id',
            labelField: 'text',
            searchField: ['text'],
        });
    }

    // ─── API: cuentas por método de pago ─────────────────────────────────────
    async function getCuentasPorMetodoPago(metodoPagoId) {
        try {
            const res = await axios.get(route('tenant.utils.getListBankAccounts', {
                payment_method_id: metodoPagoId
            }));
            if (!res.data.success) {
                toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                return null;
            }
            return res.data.bank_accounts;
        } catch (error) {
            toastr.error(error, 'ERROR EN LA PETICIÓN OBTENER CUENTAS');
            return null;
        }
    }

    // ─── Limpiar modal ────────────────────────────────────────────────────────
    function limpiarMdlPagar() {
        document.querySelector('#cliente').textContent       = '';
        document.querySelector('#numero').textContent        = '';
        document.querySelector('#monto').textContent         = '';
        document.querySelector('#saldo').textContent         = '';
        document.querySelector('#estado').textContent        = '';
        document.querySelector('#instance_info').textContent = '';
        document.querySelector('#balance_raw').value         = '0';
        document.querySelector('#observacion').value         = '';
        document.querySelector('#nro_operacion').value       = '';
        document.querySelector('#efectivo_venta').value      = '0.00';
        document.querySelector('#importe_venta').value       = '0.00';
        document.querySelector('#cantidad').value            = '0.00';

        window.pagoSelect.setValue('A CUENTA');

        limpiarImagen();
        clearValidationErrors('msgError');
    }
</script>
