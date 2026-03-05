<form action="" method="post" id="form-charge">
    <div class="row g-3">

        <div class="col-8">
            <!-- RESUMEN DE COBRO -->
            <div class="row g-2 mb-3" id="charge-summary">
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

            <div class="row">
                <!-- LADO IZQUIERDO — Métodos de pago -->
                <div class="col-lg-4 col-md-6 col-12 col-payments">
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
                <!-- CENTRO — Comprobante + Cliente + Cobrar + QR -->
                <div class="col-lg-8 col-md-6 col-12">
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
        </div>


        <!-- COLUMNA 3 — NOTIFICACIONES ENLAZABLES -->
        <div class="col-4">
            <div class="notif-card h-100 d-flex flex-column">

                <!-- Header -->
                <div class="notif-card-header d-flex align-items-center justify-content-between">
                    <span>
                        <i class="fas fa-bell me-2"></i>Notificaciones a Enlazar
                    </span>
                    <span class="badge bg-warning text-dark" id="notif-selected-count">0 sel.</span>
                </div>

                <!-- Acciones rápidas -->
                <div class="border-bottom d-flex align-items-center gap-2 px-2 py-1">
                    <button type="button" class="btn btn-outline-secondary btn-notif-xs" id="btn-clear-notif">
                        <i class="fas fa-times me-1"></i>Limpiar
                    </button>
                    <span class="text-muted ms-auto" style="font-size:.68rem;">
                        <i class="fas fa-hand-pointer me-1"></i>Selección múltiple
                    </span>
                </div>

                <!-- Tabla -->
                <div class="notif-table-wrapper flex-grow-1">
                    @include('cashier_counter.counter.tables.tbl_alerts')
                </div>

            </div>
        </div>
        <!-- /col notificaciones -->

    </div>
</form>

<style>
    /* ── Comprobante (sin cambios) ─────────────────────── */
    .comprobante-btn {
        cursor: pointer;
        transition: all .25s ease;
    }

    .comprobante-btn.active {
        border-color: #0aa2c0 !important;
        background-color: rgba(13, 202, 240, .25);
        box-shadow: 0 0 0 .15rem rgba(13, 202, 240, .35);
        transform: translateY(-2px);
    }

    .comprobante-btn.active .fw-semibold {
        color: #0aa2c0;
    }

    /* ── Card notificaciones ───────────────────────────── */
    .notif-card {
        border: 1px solid #dee2e6;
        border-radius: .55rem;
        overflow: hidden;
        background: #fff;
    }

    .notif-card-header {
        background: linear-gradient(90deg, #f0fbff, #e6f4f8);
        border-bottom: 1px solid #cfe9f0;
        padding: .55rem .85rem;
        font-size: .8rem;
        font-weight: 700;
        color: #0aa2c0;
    }

    .notif-card-footer {
        background: #f8f9fa;
        border-top: 1px solid #dee2e6;
        padding: .4rem .85rem;
        font-size: .74rem;
    }

    /* ── Tabla ─────────────────────────────────────────── */
    /* .notif-table-wrapper {
        overflow-y: auto;
        max-height: 320px;
    } */

    .notif-table-wrapper::-webkit-scrollbar {
        width: 4px;
    }

    .notif-table-wrapper::-webkit-scrollbar-thumb {
        background: #0aa2c0;
        border-radius: 10px;
    }


    /* ── Botón xs ──────────────────────────────────────── */
    .btn-notif-xs {
        padding: .12rem .4rem;
        font-size: .7rem;
        line-height: 1.4;
        border-radius: .3rem;
    }
</style>
