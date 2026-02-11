<div class="row">
    <div class="col-md-6">

        <div class="row g-3">

            <!-- PROVEEDOR -->
            <div class="col-12">
                <small class="fw-bold text-muted">
                    <i class="fas fa-user-tie text-primary me-1"></i> PROVEEDOR
                </small>
                <p id="cliente" class="fw-semibold text-dark mb-0"></p>
            </div>

            <!-- DOCUMENTO -->
            <div class="col-lg-3 col-md-4 col-sm-12 col-12">
                <small class="fw-bold text-muted">
                    <i class="fas fa-file-invoice text-secondary me-1"></i> DOCUMENTO
                </small>
                <p id="documento" class="fw-semibold mb-0"></p>
            </div>

            <!-- MONTO -->
            <div class="col-lg-3 col-md-4 col-sm-12 col-12">
                <small class="fw-bold text-muted">
                    <i class="fas fa-sack-dollar text-success me-1"></i> MONTO
                </small>
                <p id="monto" class="fw-semibold text-success mb-0"></p>
            </div>

            <!-- SALDO -->
            <div class="col-lg-3 col-md-4 col-sm-12 col-12">
                <small class="fw-bold text-muted">
                    <i class="fas fa-scale-balanced text-danger me-1"></i> SALDO
                </small>
                <p id="saldo" class="fw-semibold text-danger mb-0"></p>
            </div>

            <!-- ESTADO -->
            <div class="col-lg-3 col-md-4 col-sm-12 col-12">
                <small class="fw-bold text-muted">
                    <i class="fas fa-circle-check text-info me-1"></i> ESTADO
                </small>
                <p id="estado" class="fw-semibold mb-0"></p>
            </div>

        </div>

        <hr class="my-3">

        <div class="row justify-content-center">
            <div class="col-12" style="zoom:85%">
                <div class="table-responsive">
                    @include('accounts.supplier_accounts.tables.tbl_detalle_pago')
                </div>
            </div>
        </div>

    </div>

    <div class="col-md-6">
        @include('accounts.supplier_accounts.forms.form_pagar')
    </div>
</div>
