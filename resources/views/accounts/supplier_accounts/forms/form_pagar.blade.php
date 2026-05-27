<form id="formPagar" method="POST" enctype="multipart/form-data">
    {{ csrf_field() }}

    {{-- saldo raw para JS (tipoPago) --}}
    <input type="hidden" id="balance_raw" value="0">

    <div class="row g-4">

        {{-- PAGO --}}
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label for="pago" class="fw-bold required_field">
                <i class="fas fa-hand-holding-usd me-1 text-info"></i>PAGO
            </label>
            <select name="pago" id="pago" class="form-select" required onchange="tipoPagoProveedor(this)">
                <option value="A CUENTA">A CUENTA</option>
                <option value="TODO">TODO</option>
            </select>
            <span class="pago_error msgError" style="color:red;"></span>
        </div>

        {{-- MÉTODO DE PAGO --}}
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label for="metodo_pago" class="fw-bold required_field">
                <i class="fas fa-credit-card me-1 text-primary"></i>MÉTODO DE PAGO
            </label>
            <select name="metodo_pago" id="metodo_pago" class="form-select" required>
                <option></option>
                @foreach ($payment_methods as $metodo_pago)
                    <option value="{{ $metodo_pago->id }}" @if($metodo_pago->id == 1) selected @endif>
                        {{ $metodo_pago->description }}
                    </option>
                @endforeach
            </select>
            <span class="metodo_pago_error msgError" style="color:red;"></span>
        </div>

        {{-- FECHA --}}
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label for="fecha" class="fw-bold required_field">
                <i class="fas fa-calendar-alt me-1 text-secondary"></i>FECHA
            </label>
            <input type="date" name="fecha" id="fecha" value="<?= date('Y-m-d') ?>"
                class="form-control" required>
            <span class="fecha_error msgError" style="color:red;"></span>
        </div>

        {{-- EFECTIVO --}}
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label for="efectivo_venta" class="fw-bold required_field">
                <i class="fas fa-money-bill me-1 text-success"></i>EFECTIVO
            </label>
            <input type="text" id="efectivo_venta" name="efectivo_venta" value="0.00"
                class="form-control" onkeyup="changeEfectivo(this)" required>
            <span class="efectivo_venta_error msgError" style="color:red;"></span>
        </div>

        {{-- IMPORTE --}}
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label for="importe_venta" class="fw-bold required_field">
                <i class="fas fa-coins me-1 text-warning"></i>IMPORTE
            </label>
            <input type="text" readonly id="importe_venta" name="importe_venta" value="0.00"
                class="form-control" onkeyup="changeImporte(this)" required>
            <span class="importe_venta_error msgError" style="color:red;"></span>
        </div>

        {{-- MONTO (solo lectura) --}}
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label for="cantidad" class="fw-bold required_field">
                <i class="fas fa-calculator me-1 text-dark"></i>MONTO
                <small class="text-muted fw-normal">(efectivo + importe)</small>
            </label>
            <input type="number" min="0" id="cantidad" name="cantidad" value="0.00"
                class="form-control input-monto-readonly" readonly required>
            <span class="cantidad_error msgError" style="color:red;"></span>
        </div>

        {{-- CUENTA --}}
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label for="cuenta" class="fw-bold">
                <i class="fas fa-university me-1 text-primary"></i>CUENTA
                <span class="text-danger" id="lbl-prov-cuenta-req" style="display:none;">*</span>
            </label>
            <select id="cuenta" name="cuenta" class="form-select">
                <option value="">SELECCIONAR</option>
            </select>
            <span class="cuenta_error msgError"></span>
        </div>

        {{-- N° OPERACIÓN --}}
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label for="nro_operacion" class="fw-bold">
                <i class="fas fa-hashtag me-1 text-secondary"></i>N° OPERACIÓN
                <span class="text-danger" id="lbl-prov-nroope-req" style="display:none;">*</span>
            </label>
            <input type="text" maxlength="20" id="nro_operacion" name="nro_operacion"
                class="form-control">
            <span class="nro_operacion_error msgError"></span>
        </div>

        {{-- OBSERVACIÓN --}}
        <div class="col-12">
            <label for="observacion" class="fw-bold">
                <i class="fas fa-comment-alt me-1 text-muted"></i>OBSERVACIÓN
            </label>
            <textarea name="observacion" id="observacion" class="form-control" rows="2"></textarea>
            <span class="observacion_error msgError" style="color:red;"></span>
        </div>

        {{-- IMAGEN --}}
        <div class="col-12">
            <label for="img_pago" class="fw-bold">
                <i class="fas fa-image me-1 text-info"></i>IMAGEN
                <small class="text-muted fw-normal">(opcional)</small>
            </label>
            <input id="img_pago" name="img_pago" type="file" accept="image/*" class="filepond">
            <span class="img_pago_error msgError" style="color:red;"></span>
        </div>

    </div>
</form>

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
