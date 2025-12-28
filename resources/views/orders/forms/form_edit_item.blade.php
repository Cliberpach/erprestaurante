<form action="" id="formEditItem" method="post">
    @csrf

    <div class="row g-3">

        <!-- Card informativa del plato -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-3">

                    <div class="row text-sm">

                        <div class="col-md-6 mb-2 d-flex align-items-center">
                            <i class="fas fa-utensils text-primary me-2"></i>
                            <div>
                                <small class="text-muted d-block">Nombre</small>
                                <span class="fw-semibold" id="item_nombre_edit"></span>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2 d-flex align-items-center">
                            <i class="fas fa-layer-group text-secondary me-2"></i>
                            <div>
                                <small class="text-muted d-block">Info</small>
                                <span class="fw-semibold" id="item_tipo_plato_edit"></span>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2 d-flex align-items-center">
                            <i class="fas fa-money-bill-wave text-success me-2"></i>
                            <div>
                                <small class="text-muted d-block">Precio Compra</small>
                                <span class="fw-semibold" id="item_precio_compra_edit"></span>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2 d-flex align-items-center">
                            <i class="fas fa-tag text-warning me-2"></i>
                            <div>
                                <small class="text-muted d-block">Precio Venta</small>
                                <span class="fw-semibold" id="item_precio_venta_edit"></span>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <!-- Cantidad (único input) -->
        <div class="col-12">
            <label class="required_field fw-bold" for="item_cantidad_edit">
                Cantidad
            </label>
            <input
                required
                type="text"
                id="item_cantidad_edit"
                class="form-control inputEnteroPositivo"
                placeholder="Ingrese la cantidad"
            >
        </div>

    </div>
</form>
