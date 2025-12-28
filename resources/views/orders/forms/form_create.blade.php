<form id="form_create" method="POST">
    @csrf
    @method('POST')

    <p class="text-muted mb-2">
        <span class="text-danger">*</span> Los campos marcados son obligatorios.
    </p>

    <div class="row g-3">
        <!-- Información de Caja -->
        <div class="col-lg-12 col-md-12">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body py-3">

                    <!-- Título -->
                    <div class="d-flex align-items-center mb-3">
                        <i class="fa-solid fa-cash-register text-primary fs-4 me-2"></i>
                        <h6 class="fw-bold text-primary mb-0">Información</h6>
                    </div>

                    <div class="row align-items-center g-3">

                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">

            <div class="card border-0 shadow-sm">
                <!-- Header -->
                <div
                    class="card-header d-flex align-items-center justify-content-between bg-primary fw-bold text-white">
                    <div class="d-flex align-items-center">
                        <i class="fa-solid fa-utensils me-2"></i>
                        SELECCIONAR PLATOS
                    </div>
                </div>

                <!-- Body -->
                <div class="card-body">

                    <div class="row mb-3">
                        <div class="col-12 d-flex gap-2">
                            <!-- Botón agregar Plato -->
                            <button type="button" class="btn btn-outline-primary d-flex align-items-center gap-2"
                                onclick="openMdlDishes()">
                                <i class="fas fa-utensils"></i>
                                <span>Agregar Plato</span>
                            </button>

                            <!-- Botón agregar Producto -->
                            <button type="button" class="btn btn-outline-success d-flex align-items-center gap-2"
                                onclick="openMdlProducts()">
                                <i class="fas fa-bottle-water"></i>
                                <span>Agregar Producto</span>
                            </button>
                        </div>
                    </div>

                    <div class="row g-3">

                        <!-- Item seleccionado -->
                        <div class="col-lg-5 col-md-7">
                            <label class="form-label fw-bold">Ítem seleccionado</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fa-solid fa-box"></i>
                                </span>
                                <input id="producto" name="producto" readonly type="text" class="form-control"
                                    placeholder="Seleccione un plato o producto">
                            </div>
                        </div>

                        <!-- Stock -->
                        <div class="col-lg-2 col-md-3 col-sm-4">
                            <label class="form-label fw-bold">Stock</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fa-solid fa-warehouse"></i>
                                </span>
                                <input id="item_stock" name="item_stock" readonly type="text" class="form-control">
                            </div>
                        </div>

                        <!-- Precio compra -->
                        <div class="col-lg-2 col-md-3 col-sm-4">
                            <label class="form-label fw-bold">Precio Compra</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fa-solid fa-coins"></i>
                                </span>
                                <input id="purchase_price" name="purchase_price" readonly type="text"
                                    class="form-control" placeholder="S/ 0.00">
                            </div>
                        </div>

                        <!-- Precio venta -->
                        <div class="col-lg-2 col-md-3 col-sm-4">
                            <label class="form-label fw-bold">Precio Venta</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fa-solid fa-tag"></i>
                                </span>
                                <input id="sale_price" name="sale_price" readonly type="text" class="form-control"
                                    placeholder="S/ 0.00">
                            </div>
                        </div>

                        <!-- Cantidad -->
                        <div class="col-lg-2 col-md-3 col-sm-4">
                            <label class="form-label fw-bold required_field">Cantidad</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fa-solid fa-layer-group"></i>
                                </span>
                                <input id="cantidad" name="cantidad" type="text"
                                    class="form-control inputEnteroPositivo" placeholder="Cantidad">
                            </div>
                        </div>

                    </div>


                    <!-- Acción -->
                    <div class="d-flex justify-content-end mt-4">
                        <button class="btn btn-primary btnAgregarProducto px-4" type="button">
                            <i class="fa-solid fa-cart-plus me-1"></i>
                            Agregar
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </div>


    <div class="row mt-3">
        <div class="col-12 mb-3 mt-3">
            <div class="card">
                <div class="card-header" style="background-color: rgb(0, 102, 255);font-weight:bold;color:white;">
                    DETALLE DEL PEDIDO
                </div>
                <div class="card-body">

                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                @include('orders.tables.tbl_order_detail')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 d-flex justify-content-lg-end">
            <div class="col-12 col-lg-4">
                <div class="table-responsive">
                    @include('orders.tables.tbl_amounts')
                </div>
            </div>
        </div>
    </div>

</form>
