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

                        <!-- Cliente -->
                        <div class="col-lg-6 col-md-8 col-sm-12">
                            <label class="form-label fw-bold required_field">Cliente:</label>
                            <i class="fas fa-plus btn btn-warning btn-sm" onclick="openMdlNewCustomer();"
                                style="margin-left:4px;"></i>

                            <select class="form-control" id="client_id" name="client_id" required>
                                <option value="">Seleccione un cliente</option>
                            </select>
                            <p class="client_id_error msgError mb-0"></p>
                        </div>

                        <!-- Observación -->
                        <div class="col-lg-6 col-md-8 col-sm-12">
                            <label class="form-label fw-bold">Observación:</label>

                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fa-solid fa-comment-dots"></i>
                                </span>
                                <textarea class="form-control input-fill" id="observation" name="observation" rows="2" maxlength="500"
                                    placeholder="Ingrese una observación (opcional)"></textarea>
                            </div>
                            <p class="observation_error msgError mb-0"></p>
                        </div>

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
                        <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                            <label class="form-label fw-bold">Ítem seleccionado</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fa-solid fa-box"></i>
                                </span>
                                <input id="producto" readonly type="text" class="form-control"
                                    placeholder="Item seleccionado">
                            </div>
                        </div>

                        <!-- Stock -->
                        <div class="col-lg-2 col-md-2 col-sm-3 col-3">
                            <label class="form-label fw-bold">Stock</label>
                            <input id="item_stock" readonly type="text" class="form-control">

                            {{-- <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fa-solid fa-warehouse"></i>
                                </span>
                                <input id="item_stock" name="item_stock" readonly type="text" class="form-control">
                            </div> --}}
                        </div>

                        <!-- Precio compra -->
                        {{-- <div class="col-lg-2 col-md-3 col-sm-4">
                            <label class="form-label fw-bold">Precio Compra</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fa-solid fa-coins"></i>
                                </span>
                                <input id="purchase_price" name="purchase_price" readonly type="text"
                                    class="form-control" placeholder="S/ 0.00">
                            </div>
                        </div> --}}

                        <!-- Precio venta -->
                        <div class="col-lg-2 col-md-2 col-sm-3 col-3">
                            <label class="form-label fw-bold">P.Venta</label>
                            <input id="sale_price" readonly type="text" class="form-control" placeholder="">
                            {{-- <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fa-solid fa-tag"></i>
                                </span>
                                <input id="sale_price" name="sale_price" readonly type="text" class="form-control"
                                    placeholder="S/ 0.00">
                            </div> --}}
                        </div>

                        <!-- Cantidad -->
                        <div class="col-lg-2 col-md-2 col-sm-3 col-3">
                            <label class="form-label fw-bold">Cantidad</label>
                            <input id="cantidad" type="text" class="form-control inputEnteroPositivo input-fill"
                                placeholder="">
                            {{-- <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fa-solid fa-layer-group"></i>
                                </span>
                                <input id="cantidad" name="cantidad" type="text"
                                    class="form-control inputEnteroPositivo" placeholder="Cantidad">
                            </div> --}}
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-3 col-3" style="margin:auto 0 0 0;">
                            <button class="btn btn-primary btnAgregarProducto px-4" type="button">
                                <i class="fa-solid fa-cart-plus me-1"></i>
                            </button>
                        </div>

                        <div class="col-lg-6 col-md-8 col-sm-12">
                            <label class="form-label fw-bold">Observación:</label>

                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fa-solid fa-comment-dots"></i>
                                </span>
                                <textarea class="form-control input-fill" id="observation_item" rows="2" maxlength="20"
                                    placeholder="Ingrese una observación (opcional)"></textarea>
                            </div>
                            <p class="observation_error msgError mb-0"></p>
                        </div>
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
                            @include('orders.cards.card-detail')
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-12 tex-align-end">
            <div class="table-responsive">
                @include('orders.tables.tbl_amounts')
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="row">
                <div class="col-lg-9 col-md-9 col-sm-6 col-12 mb-3">
                    <label for="payment_method" class="form-label fw-bold">Método de Pago</label>
                    <select name="payment_method" id="payment_method" class="form-control">
                        <option value="">Seleccionar método de pago</option>
                        @foreach ($payment_methods as $method)
                            <option value="{{ $method->id }}">{{ $method->description }}</option>
                        @endforeach
                    </select>
                    <p class="payment_method_error msgError mb-0"></p>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-12 mb-3" style="margin:auto 0 0 0;">
                    <button type="button" class="btn btn-primary btn-sm btn-view-qr mt-2">
                        <i class="fas fa-qrcode mr-1"></i> Ver QR
                    </button>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-qrcode text-success me-1"></i> Voucher Pago
                    </label>

                    <input capture="environment" type="file" class="form-control" name="voucher" id="voucher"
                        accept=".jpg,.jpeg,.png,image/jpeg,image/png">

                    <small class="text-secondary fst-italic">
                        Máx. 4 MB — JPG / JPEG / PNG
                    </small>
                    <span class="voucher_error msgError text-danger"></span>
                </div>
            </div>

        </div>
    </div>

</form>
