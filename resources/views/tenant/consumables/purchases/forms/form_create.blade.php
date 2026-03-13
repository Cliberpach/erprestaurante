<form action="" id="formStorePurchase" method="post">
    <div class="row">
        @csrf

        <div class="row">

            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 mb-3">
                <label class="form-label fw-bold required_field">Condición:</label>
                <select class="form-control" id="payment_condition_id" name="payment_condition_id" required>
                    @foreach ($payment_conditions as $payment_condition)
                        <option value="{{ $payment_condition->id }}" data-days="{{ $payment_condition->nro_days ?? 0 }}"
                            @if ($payment_condition->name === 'CONTADO') selected @endif>

                            @if ($payment_condition->type === 'CONTADO')
                                {{ $payment_condition->name }}
                            @else
                                {{ $payment_condition->name . '-' . $payment_condition->nro_days . ' DÍAS ' }}
                            @endif

                        </option>
                    @endforeach
                </select>
                <p class="payment_condition_id_error msgError mb-0"></p>
            </div>

            <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
                <label for="fecha_registro" class="required_field" style="font-weight: bold;">FECHA REGISTRO</label>
                <input value="{{ date('Y-m-d') }}" readonly required id="fecha_registro" name="fecha_registro"
                    type="date" class="form-control" aria-label="Username" aria-describedby="basic-addon1">
            </div>

            <!-- Fecha Vencimiento -->
            <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12 mb-3">
                <label class="form-label fw-bold">Fecha Vencimiento:</label>
                <input type="date" class="form-control input-fill" name="expiration_date" id="expiration_date"
                    value="{{ date('Y-m-d') }}">
            </div>

            <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
                <label for="fecha_registro" class="required_field" style="font-weight: bold;">FECHA ENTREGA</label>
                <input value="{{ date('Y-m-d') }}" required id="fecha_entrega" name="fecha_entrega" type="date"
                    class="form-control input-fill" aria-label="Username" aria-describedby="basic-addon1">
            </div>

            <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 mb-3">
                <label class="required_field" for="tipo_doc" style="font-weight: bold;">TIPO DOC</label>
                <select required name="tipo_doc" id="tipo_doc" data-placeholder="Seleccionar">
                    <option value=""></option>
                    <option value="FACTURA">FACTURA</option>
                    <option value="BOLETA">BOLETA</option>
                </select>
            </div>

            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 mb-3">
                <label class="required_field" for="proveedor" style="font-weight: bold;">PROVEEDOR</label>
                <i class="fa-solid fa-plus btn btn-primary btn-sm rounded-circle mb-1"
                    onclick="openMdlNuevoProveedor();"></i>
                <select required name="proveedor" id="proveedor" data-placeholder="Seleccionar">
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">
                            {{ $supplier->type_document_abbreviation . ':' . $supplier->document_number . '-' . $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
                <div>
                    <label for="cost_center" class="form-label" style="font-weight: bold;">Centro de costos</label>
                    <i class="fa-solid fa-plus btn btn-primary btn-sm rounded-circle mb-1"
                        onclick="openMdlCostCenter();"></i>
                </div>
                <select name="cost_center" id="cost_center" class="form-control" data-placeholder="Seleccionar">
                    <option value=""></option>
                    @foreach ($cost_center as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </select>
                <p class="cost_center_error msgError"></p>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
                <label class="required_field mb-2" for="serie" style="font-weight: bold;">Serie</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fa-solid fa-envelopes-bulk"></i>
                    </span>
                    <input required id="serie" name="serie" type="text" class="form-control input-fill"
                        placeholder="Serie" aria-label="Username" aria-describedby="basic-addon1">
                </div>
            </div>

            <div class="col-lg-2 col-md-6 col-sm-12 col-xs-12 mb-3">
                <label class="required_field mb-2" for="numero" style="font-weight: bold;">N°</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fa-solid fa-hashtag"></i>
                    </span>
                    <input required id="numero" name="numero" type="text"
                        class="form-control inputEnteroPositivo input-fill" placeholder="Número"
                        aria-label="Username" aria-describedby="basic-addon1">
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 mb-3">
                <label for="observation" style="font-weight: bold;">OBSERVACIÓN</label>
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-text-width"></i>
                    </span>
                    <div class="form-floating">
                        <textarea class="form-control input-fill" placeholder="Escribir..." id="observation" name="observation"></textarea>
                        <label for="observation">Máximo 200 caracteres</label>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                <label for="igv_chk" style="font-weight: bold;">Igv</label>
                <div class="form-check">
                    <input checked id="igv_chk" name="igv_chk" class="form-check-input" type="checkbox"
                        value="{{ $igv }}">
                    <label class="form-check-label" for="igv_chk">
                        {{ number_format($igv, 2) }}%
                    </label>
                </div>
            </div>

            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                <label for="discount_cash" style="font-weight: bold;">Descontar Caja</label>
                <div class="form-check">
                    <input checked id="discount_cash" name="discount_cash" class="form-check-input" type="checkbox"
                        value="SI">
                    <label class="form-check-label" for="discount_cash">
                        Sí
                    </label>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 mb-3" style="display: none;">
                <label class="required_field" for="moneda" style="font-weight: bold;">MONEDA</label>
                <select name="moneda" id="moneda" data-placeholder="Seleccionar" class="select2_form">
                    <option value=""></option>
                    <option value="PEN" selected>SOLES</option>
                    <option value="USD">DÓLARES</option>
                </select>
            </div>

        </div>

        <div class="row">
            <div class="col-12 mb-3 mt-3">
                <div class="card">
                    <div class="card-header" style="background-color: rgb(0, 102, 255);font-weight:bold;color:white;">
                        SELECCIONAR INSUMOS
                    </div>
                    <div class="card-body">

                        <div class="row">
                            <div class="col-lg-5 col-md-7 col-sm-12 col-xs-12">
                                <label for="categoria" style="font-weight: bold;">INSUMO</label>

                                <div class="input-group mb-3">
                                    <input id="producto" readonly type="text"
                                        class="form-control" placeholder="Producto" aria-label="Recipient's username"
                                        aria-describedby="button-addon2">
                                    <button class="btn btn-primary" type="button" id="button-addon2"
                                        onclick="openMdlProducts()">
                                        <i class="fa-solid fa-magnifying-glass"></i> Buscar
                                    </button>
                                </div>
                            </div>

                            {{-- <div class="col-lg-3 col-md-5 col-sm-12 col-xs-12">
                                <label for="categoria" style="font-weight: bold;">UNIDAD</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon1">
                                        <i class="fa-solid fa-layer-group"></i>
                                    </span>
                                    <input id="unidad" name="unidad" readonly type="text" class="form-control" placeholder="Unidad" aria-label="Username" aria-describedby="basic-addon1">
                                  </div>
                            </div> --}}

                            <div class="col-lg-3 col-md-5 col-sm-12 col-xs-12">
                                <label for="precio" style="font-weight: bold;">PRECIO</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon1">
                                        <i class="fa-solid fa-money-bill-1-wave"></i>
                                    </span>
                                    <input id="precio" type="text"
                                        class="form-control inputDecimalPositivo input-fill" placeholder="Precio"
                                        aria-label="Username" aria-describedby="basic-addon1">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                                <label for="categoria" style="font-weight: bold;">CANTIDAD</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon1">
                                        <i class="fa-solid fa-box-open"></i>
                                    </span>
                                    <input id="cantidad" type="text"
                                        class="form-control inputDecimalPositivo input-fill" placeholder="Cantidad"
                                        aria-label="Username" aria-describedby="basic-addon1">
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-end">
                            <div class="col-3 d-flex justify-content-end">
                                <button class="btn btn-primary btnAgregarProducto" type="button">
                                    <i class="fa-solid fa-cart-plus"></i> AGREGAR
                                </button>
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
                        DETALLE DE LA COMPRA
                    </div>
                    <div class="card-body">

                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    @include('purchases.purchase_document.tables.tbl_purchase_document_detail')
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-end">
                            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">

                                @include('purchases.purchase_document.tables.tbl_purchase_document_amounts')

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    </div>
</form>
