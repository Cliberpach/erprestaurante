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
                        <h6 class="fw-bold text-primary mb-0">Información de Caja</h6>
                    </div>

                    <div class="row align-items-center g-3">

                        <!-- Selector de Caja -->
                        <div class="col-lg-4 col-md-6">
                            <label for="cash_available_id" class="form-label fw-semibold required_field">
                                Caja Abierta
                            </label>
                            <select disabled required name="cash_available_id" class="selectCajas form-select"
                                id="cash_available_id">
                                <option value="">Seleccionar caja</option>
                            </select>
                            <small class="text-danger cash_available_id_error"></small>
                        </div>

                        <!-- Cajero -->
                        <div class="col-lg-4 col-md-6">
                            <div class="d-flex align-items-center info-box">
                                <i class="fa-solid fa-user-cash text-primary fs-5 me-2"></i>
                                <div>
                                    <small class="text-muted d-block">Cajero</small>
                                    <span class="fw-semibold" id="info_cajero">—</span>
                                </div>
                            </div>
                        </div>

                        <!-- Fecha Apertura -->
                        <div class="col-lg-4 col-md-6">
                            <div class="d-flex align-items-center info-box">
                                <i class="fa-solid fa-calendar-day text-success fs-5 me-2"></i>
                                <div>
                                    <small class="text-muted d-block">Apertura</small>
                                    <span class="fw-semibold" id="info_fecha_apertura">—</span>
                                </div>
                            </div>
                        </div>

                        <!-- Turno -->
                        <div class="col-lg-4 col-md-6">
                            <div class="d-flex align-items-center info-box">
                                <i class="fa-solid fa-clock text-warning fs-5 me-2"></i>
                                <div>
                                    <small class="text-muted d-block">Turno</small>
                                    <span class="fw-semibold" id="info_turno">—</span>
                                </div>
                            </div>
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

                    <div class="row g-3 align-items-end">

                        <!-- Plato -->
                        <div class="col-lg-5 col-md-7">
                            <label class="form-label fw-bold">Plato</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fa-solid fa-utensils"></i>
                                </span>
                                <input id="producto" name="producto" readonly type="text" class="form-control"
                                    placeholder="Seleccione un plato">
                                <button class="btn btn-outline-primary" type="button" onclick="openMdlProducts()">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Precio compra -->
                        <div class="col-lg-3 col-md-5">
                            <label class="form-label fw-bold">Precio Compra</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fa-solid fa-money-bill-wave"></i>
                                </span>
                                <input id="purchase_price" name="purchase_price" readonly type="text"
                                    class="form-control" placeholder="S/ 0.00">
                            </div>
                        </div>

                        <!-- Precio venta -->
                        <div class="col-lg-3 col-md-5">
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
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-bold required_field">Cantidad</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fa-solid fa-box-open"></i>
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
                    DETALLE DE LA PROGRAMACIÓN
                </div>
                <div class="card-body">

                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                @include('supply.programming.tables.tbl_note_income_detail')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</form>
