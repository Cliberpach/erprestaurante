<form action="" method="post" id="form-charge">
    <div class="row g-3">

        <!-- TOTAL -->
        <div class="col-12 text-start">
            <h3 class="fw-bold text-celeste-dark">
                <i class="fas fa-money-bill-wave me-1"></i>
                TOTAL: <span id="total-mdlcharge"></span>
            </h3>
        </div>

        <!-- LADO IZQUIERDO -->
        <div class="col-lg-6 col-md-6 col-12">
            <div class="row g-3">

                <div class="col-12">
                    <label class="form-label">
                        <i class="fas fa-credit-card text-info me-1"></i>
                        Método de Pago
                    </label>
                    <select class="form-control" id="payment_method_mdlcharge">
                        @foreach ($payment_methods as $payment_method)
                            <option value="{{ $payment_method->id }}">
                                {{ $payment_method->description }}
                            </option>
                            <option value="MIXTO">MIXTO</option>
                        @endforeach
                    </select>
                </div>

                @foreach ($payment_methods as $payment_method)
                    <div class="col-12">
                        <label class="form-label">
                            <i class="fas fa-money-bill-wave text-info me-1"></i>
                            {{ $payment_method->description }}
                        </label>

                        <input disabled type="number" step="0.01" min="0"
                            class="form-control input-payment input-payment-{{ $payment_method->id }}"
                            placeholder="Ingrese un monto" data-id="{{ $payment_method->id }}">
                    </div>
                @endforeach

                <!-- VUELTO -->
                <div class="col-6">
                    <div class="bg-celeste-soft h-100 rounded border p-3 text-center">
                        <div class="small text-muted">
                            <i class="fas fa-hand-holding-usd text-info me-1"></i>
                            VUELTO
                        </div>
                        <div class="fs-4 fw-bold text-info change_mdlCharge">
                            0.00
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- LADO DERECHO -->
        <div class="col-lg-6 col-md-6 col-12">
            <div class="row g-3">

                <!-- COMPROBANTE -->
                <div class="col-12">
                    <label class="form-label">
                        <i class="fas fa-file-invoice text-info me-1"></i>
                        Tipo de Comprobante
                    </label>

                    <div class="row g-2 text-center">
                        @foreach ($invoice_types as $index => $item)
                            <div class="col-lg-4 col-md-4 col-12">
                                <div class="comprobante-btn comprobante-bg-{{ $index % 3 }} h-100 rounded border p-3"
                                    id="invoice-type-{{ $item->id }}" data-id="{{ $item->id }}">
                                    <div class="fw-semibold text-celeste-dark">
                                        {{ $item->name }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <p class="invoice_id_mdlcharge_error msgError mb-0"></p>
                    </div>

                </div>

                <!-- CLIENTE -->
                <div class="col-12">
                    <label class="form-label">
                        <i class="fas fa-user text-info me-1"></i>
                        Cliente
                    </label>
                    <i class="fas fa-plus btn btn-warning btn-sm" onclick="openMdlNewCustomer();" style="margin-left:4px;"></i>
                    <select class="form-control" id="customer_id_mdlcharge" name="customer_id"></select>
                    <p class="customer_id_mdlcharge_error msgError mb-0"></p>
                </div>

                <!-- COBRAR -->
                <div class="col-12 d-grid">
                    <button type="submit" class="btn btn-info btn-lg btn-celeste text-white">
                        <i class="fas fa-cash-register me-1"></i>
                        COBRAR
                    </button>
                </div>

            </div>
        </div>

    </div>
</form>
<style>
    .comprobante-btn {
        cursor: pointer;
        transition: all .25s ease;
    }

    /* RESALTADO */
    .comprobante-btn.active {
        border-color: #0aa2c0 !important;
        background-color: rgba(13, 202, 240, 0.25);
        box-shadow: 0 0 0 .15rem rgba(13, 202, 240, 0.35);
        transform: translateY(-2px);
    }

    /* Texto más fuerte cuando está activo */
    .comprobante-btn.active .fw-semibold {
        color: #0aa2c0;
    }
</style>
