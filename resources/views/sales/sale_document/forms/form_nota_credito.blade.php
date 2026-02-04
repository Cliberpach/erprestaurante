<form action="" id="formNotaCredito">

    <!-- MOTIVO (PRIORIDAD) -->
    <div class="card border-primary mb-3 shadow-sm">
        <div class="card-body">
            <label class="form-label fw-bold text-primary">
                <i class="fas fa-pen text-warning"></i> MOTIVO DE LA NOTA DE CRÉDITO
            </label>
            <textarea name="motive" id="motive" required class="form-control" rows="4" placeholder="Ingrese el motivo de la anulación..."></textarea>
        </div>
    </div>

    <!-- DETALLE DEL COMPROBANTE -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="text-primary mb-4">
                <i class="fas fa-file-invoice text-info"></i> Detalle del Comprobante
            </h5>

            <div class="row g-3">

                <!-- COLUMNA 1 -->
                <div class="col-12 col-lg-6">
                    <p><i class="fas fa-user text-primary"></i> <strong>Cliente:</strong> <span
                            id="nc_customer_name">—</span></p>
                    <p><i class="fas fa-id-card text-secondary"></i> <strong>Documento:</strong> <span
                            id="nc_customer_doc">—</span></p>
                    <p><i class="fas fa-hashtag text-dark"></i> <strong>N° Documento:</strong> <span
                            id="nc_doc">—</span></p>


                    <hr>

                    <p><i class="fas fa-receipt text-info"></i> <strong>Tipo:</strong> <span id="nc_type_sale">—</span>
                    </p>
                    <p><i class="fas fa-layer-group text-primary"></i> <strong>Serie:</strong> <span
                            id="nc_serie">—</span></p>
                    <p><i class="fas fa-sort-numeric-up text-dark"></i> <strong>Correlativo:</strong> <span
                            id="nc_correlative">—</span></p>
                </div>

                <!-- COLUMNA 2 -->
                <div class="col-12 col-lg-6">
                    <p>
                        <i class="fas fa-cash-register text-primary"></i> <strong>Caja:</strong> <span
                            id="nc_petty_cash">—</span>
                    </p>

                    <hr>

                    <p><i class="fas fa-calculator text-info"></i> <strong>Subtotal:</strong> S/ <span
                            id="nc_subtotal">—</span></p>
                    <p><i class="fas fa-percent text-warning"></i> <strong>IGV:</strong> <span
                            id="nc_igv_percent">—</span>%</p>
                    <p><i class="fas fa-receipt text-danger"></i> <strong>IGV Monto:</strong> S/ <span
                            id="nc_igv_amount">—</span></p>
                    <p>
                        <i class="fas fa-money-bill-wave text-success"></i>
                        <strong>Total:</strong>
                        <strong class="text-success">S/ <span id="nc_total">—</span></strong>
                    </p>

                    <hr>

                    <p>
                        <i class="fas fa-cloud text-primary"></i>
                        <strong>SUNAT:</strong>
                        <span id="nc_sunat_status" class="badge bg-secondary">—</span>
                    </p>

                    <p>
                        <i class="fas fa-circle-info text-info"></i>
                        <strong>Estado:</strong>
                        <span id="nc_status" class="badge bg-secondary">—</span>
                    </p>

                    <p>
                        <i class="fas fa-wallet text-success"></i>
                        <strong>Pago:</strong>
                        <span id="nc_pay_status" class="badge bg-secondary">—</span>
                    </p>
                </div>

            </div>
        </div>
    </div>

</form>
