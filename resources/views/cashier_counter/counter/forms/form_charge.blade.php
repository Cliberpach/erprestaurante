<form action="" method="post" id="form-charge">
    <div class="row g-3">

        <!-- RESUMEN DE COBRO -->
        <div class="col-12">
            <div class="row g-2" id="charge-summary">

                <!-- Total -->
                <div class="col-3">
                    <div class="rounded-3 bg-primary border-primary border bg-opacity-10 px-2 py-2 text-center">
                        <div class="text-primary mb-1" style="font-size:.65rem;letter-spacing:.05em;font-weight:600;">
                            <i class="fas fa-receipt me-1"></i>TOTAL
                        </div>
                        <div class="fw-bold text-primary fs-6" id="summary-total">0.00</div>
                    </div>
                </div>

                <!-- Pagado -->
                <div class="col-3">
                    <div class="rounded-3 bg-success border-success border bg-opacity-10 px-2 py-2 text-center">
                        <div class="text-success mb-1" style="font-size:.65rem;letter-spacing:.05em;font-weight:600;">
                            <i class="fas fa-circle-check me-1"></i>PAGADO
                        </div>
                        <div class="fw-bold text-success fs-6" id="summary-paid">0.00</div>
                    </div>
                </div>

                <!-- Pendiente -->
                <div class="col-3" id="summary-pending-box">
                    <div class="rounded-3 bg-warning border-warning border bg-opacity-10 px-2 py-2 text-center">
                        <div class="text-warning mb-1" style="font-size:.65rem;letter-spacing:.05em;font-weight:600;">
                            <i class="fas fa-hourglass-half me-1"></i>PENDIENTE
                        </div>
                        <div class="fw-bold text-warning fs-6" id="summary-pending">0.00</div>
                    </div>
                </div>

                <!-- Vuelto -->
                <div class="col-3" id="summary-change-box">
                    <div class="rounded-3 bg-info border-info border bg-opacity-10 px-2 py-2 text-center">
                        <div class="text-info mb-1" style="font-size:.65rem;letter-spacing:.05em;font-weight:600;">
                            <i class="fas fa-hand-holding-dollar me-1"></i>VUELTO
                        </div>
                        <div class="fw-bold text-info fs-6" id="summary-change">0.00</div>
                    </div>
                </div>

            </div>
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
                        @endforeach
                        <option value="MIXTO">MIXTO</option>
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
                                    <div class="fw-semibold text-celeste-dark">{{ $item->name }}</div>
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
                    <i class="fas fa-plus btn btn-warning btn-sm" onclick="openMdlNewCustomer();"
                        style="margin-left:4px;"></i>
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

                <!-- QR PREVIEW -->
                <div class="col-12">
                    <div class="qr-box rounded-4 p-4 text-center">
                        <div class="qr-header mb-2">
                            <i class="fas fa-qrcode me-1"></i>
                            Img Pago
                        </div>
                        <a href="#" id="qr-link-preview" data-lightbox="qr-preview">
                            <img src="" id="qr-img-preview" class="img-fluid qr-img" draggable="false"
                                style="max-height:200px;object-fit:contain;max-width:200px;" alt="Img pago">
                        </a>
                        <div class="qr-helper small mt-2">Click para ver en pantalla completa</div>
                    </div>
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
