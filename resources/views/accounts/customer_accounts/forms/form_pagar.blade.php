<div class="row">
    <div class="col-lg-6 col-md-12 col-xs-12 col-sm-12">

        <!-- DATOS DEL CLIENTE -->
        <div class="row g-3">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header py-2 text-white">
                        <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información</h6>
                    </div>

                    <div class="card-body">
                        <div class="row g-4">

                            <!-- Cliente -->
                            <div class="col-12">
                                <label class="fw-bold text-muted small">
                                    <i class="fas fa-user me-1"></i>CLIENTE
                                </label>
                                <p class="mb-0 fw-semibold" id="cliente"></p>
                            </div>

                            <!-- Documento -->
                            <div class="col-md-6">
                                <label class="fw-bold text-muted small">
                                    <i class="fas fa-file-alt me-1 text-success"></i>DOCUMENTO
                                </label>
                                <p class="mb-0" id="numero"></p>
                            </div>

                            <!-- Pedido (instance_serie + type_instance) -->
                            <div class="col-md-6">
                                <label class="fw-bold text-muted small">
                                    <i class="fas fa-shopping-bag me-1 text-primary"></i>PEDIDO
                                </label>
                                <p class="mb-0" id="instance_info"></p>
                            </div>

                            <!-- Monto total -->
                            <div class="col-md-6">
                                <label class="fw-bold text-muted small">
                                    <i class="fas fa-money-bill-wave me-1 text-success"></i>MONTO TOTAL
                                </label>
                                <p class="mb-0" id="monto"></p>
                            </div>

                            <!-- Saldo -->
                            <div class="col-md-6">
                                <label class="fw-bold text-muted small">
                                    <i class="fas fa-balance-scale me-1 text-warning"></i>SALDO PENDIENTE
                                </label>
                                <p class="mb-0 fw-bold text-danger" id="saldo"></p>
                            </div>

                            <!-- Estado -->
                            <div class="col-md-6">
                                <label class="fw-bold text-muted small">
                                    <i class="fas fa-circle me-1 text-info"></i>ESTADO
                                </label>
                                <p class="mb-0" id="estado"></p>
                            </div>

                            <!-- PDF -->
                            <div class="col-md-6 d-flex align-items-end">
                                <a class="btn btn-danger w-100 btn-sm d-flex justify-content-center align-items-center gap-2 shadow-sm"
                                    id="btn-detalle" target="_blank">
                                    <i class="fas fa-file-pdf fa-lg"></i>
                                    <span>PDF</span>
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLA HISTORIAL -->
        <div class="row justify-content-center mt-4">
            <div class="col-12" style="zoom: 85%;">
                <div class="table-responsive">
                    <table class="table-striped table-bordered table-hover dataTables-detalle text-uppercase table">
                        <thead>
                            <tr>
                                <th class="text-center">Fecha</th>
                                <th class="text-center">Observación</th>
                                <th class="text-center">Monto</th>
                                <th class="text-center">Imágen</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- FORM DE REGISTRO DE PAGO -->
    <div class="col-lg-6 col-md-12 col-xs-12 col-sm-12">
        <form id="frmDetalle" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            {{-- saldo raw para JS --}}
            <input type="hidden" id="balance_raw" value="0">

            <div class="row g-4">

                <!-- PAGO -->
                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                    <label for="pago" class="fw-bold required_field">
                        <i class="fas fa-hand-holding-usd me-1 text-info"></i>PAGO
                    </label>
                    <select id="pago" name="pago" class="form-select" required onchange="tipoPago(this)">
                        <option value="A CUENTA">A CUENTA</option>
                        <option value="TODO">TODO</option>
                    </select>
                    <span class="pago_error msgError"></span>
                </div>

                <!-- MÉTODO DE PAGO -->
                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                    <label for="modo_pago" class="fw-bold required_field">
                        <i class="fas fa-credit-card me-1 text-primary"></i>MÉTODO DE PAGO
                    </label>
                    <select id="modo_pago" name="modo_pago" class="form-select" onchange="changeModoPago(this)" required>
                        <option value=""></option>
                        @foreach ($payment_methods as $payment_method)
                            <option value="{{ $payment_method->id }}" @if ($payment_method->id == 1) selected @endif>
                                {{ $payment_method->description }}
                            </option>
                        @endforeach
                    </select>
                    <span class="modo_pago_error msgError"></span>
                </div>

                <!-- FECHA -->
                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                    <label for="fecha" class="fw-bold required_field">
                        <i class="fas fa-calendar-alt me-1 text-secondary"></i>FECHA
                    </label>
                    <input type="date" id="fecha" name="fecha" value="<?= date('Y-m-d') ?>"
                        class="form-control" required>
                    <span class="fecha_error msgError"></span>
                </div>

                <!-- EFECTIVO -->
                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                    <label class="fw-bold required_field">
                        <i class="fas fa-money-bill me-1 text-success"></i>EFECTIVO
                    </label>
                    <input type="text" id="efectivo_venta" name="efectivo_venta" value="0.00"
                        class="form-control"
                        onkeypress="return parseFloat(event, this);" onkeyup="changeEfectivo()">
                    <span class="efectivo_venta_error msgError"></span>
                </div>

                <!-- IMPORTE -->
                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                    <label for="importe_venta" class="fw-bold required_field">
                        <i class="fas fa-coins me-1 text-warning"></i>IMPORTE
                    </label>
                    <input type="text" readonly id="importe_venta" name="importe_venta" value="0.00"
                        class="form-control"
                        onkeypress="return parseFloat(event, this);" onkeyup="changeImporte()">
                    <span class="importe_venta_error msgError"></span>
                </div>

                <!-- MONTO (solo lectura, suma de efectivo + importe) -->
                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                    <label for="cantidad" class="fw-bold required_field">
                        <i class="fas fa-calculator me-1 text-dark"></i>MONTO
                        <small class="text-muted fw-normal">(efectivo + importe)</small>
                    </label>
                    <input type="number" id="cantidad" min="0" value="0.00" name="cantidad"
                        class="form-control input-monto-readonly" readonly required>
                    <span class="cantidad_error msgError"></span>
                </div>

                <!-- CUENTA -->
                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                    <label for="cuenta" class="fw-bold">
                        <i class="fas fa-university me-1 text-primary"></i>CUENTA
                        <span class="text-danger" id="lbl-cuenta-req" style="display:none;">*</span>
                    </label>
                    <select id="cuenta" name="cuenta" class="form-select">
                        <option value="">SELECCIONAR</option>
                    </select>
                    <span class="cuenta_error msgError"></span>
                </div>

                <!-- N° OPERACIÓN -->
                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                    <label for="nro_operacion" class="fw-bold">
                        <i class="fas fa-hashtag me-1 text-secondary"></i>N° OPERACIÓN
                        <span class="text-danger" id="lbl-nroope-req" style="display:none;">*</span>
                    </label>
                    <input type="text" maxlength="20" id="nro_operacion" name="nro_operacion"
                        class="form-control">
                    <span class="nro_operacion_error msgError"></span>
                </div>

                <!-- OBSERVACIÓN -->
                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                    <label for="observacion" class="fw-bold">
                        <i class="fas fa-comment-alt me-1 text-muted"></i>OBSERVACIÓN
                    </label>
                    <textarea id="observacion" name="observacion" class="form-control" rows="2"></textarea>
                    <span class="observacion_error msgError"></span>
                </div>

                <!-- IMAGEN (siempre opcional) -->
                <div class="col-12">
                    <label for="imagen" class="fw-bold">
                        <i class="fas fa-image me-1 text-info"></i>IMAGEN
                        <small class="text-muted fw-normal">(opcional)</small>
                    </label>
                    <input accept="image/*" data-max-files="1" data-allow-reorder="true" data-max-file-size="3MB"
                        id="imagen" type="file" name="imagen" class="filepond">
                    <span class="imagen_error msgError"></span>
                </div>

            </div>

        </form>
    </div>
</div>

<style>
    .input-monto-readonly {
        background-color: #e8f4e8 !important;
        color: #155724 !important;
        border-color: #a3cfbb !important;
        font-weight: 700;
        cursor: not-allowed;
    }

    [data-theme="dark"] .input-monto-readonly {
        background-color: #0f2a1a !important;
        color: #6ee7a0 !important;
        border-color: #1a4d2e !important;
    }
</style>
