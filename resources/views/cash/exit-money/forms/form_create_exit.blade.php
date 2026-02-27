<form id="form-create-exit-money" method="POST">
    @csrf


    <!-- HEADER -->
    <div class="card-header border-0 py-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-money-bill-wave text-primary fs-5"></i>
                <h5 class="fw-semibold mb-0">Registrar Egreso</h5>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('tenant.cajas.egresos.index') }}" class="btn btn-danger px-3">
                    <i class="fa-solid fa-arrow-left me-1"></i> Cancelar
                </a>

                <button type="submit" class="btn btn-primary px-4">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Guardar
                </button>
            </div>
        </div>
    </div>

    <div class="card-body">

        <!-- PRIMERA FILA -->
        <div class="row g-4">

            <!-- Tipo Comprobante -->
            <div class="col-lg-3 col-md-3 col-sm-4 col-12">
                <label class="form-label fw-bold">
                    <i class="fa-solid fa-file-invoice text-secondary me-1"></i>
                    Tipo de comprobante
                </label>
                <select name="proof_payment" id="proof_payment" class="form-select">
                    @foreach ($proof_payments as $proof_payment)
                        <option value="{{ $proof_payment->id }}">
                            {{ $proof_payment->description }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Número -->
            <div class="col-lg-3 col-md-3 col-sm-4 col-12">
                <label class="form-label fw-bold">
                    <i class="fa-solid fa-hashtag text-secondary me-1"></i>
                    Número
                </label>
                <input required type="text" name="number" id="number" class="form-control input-fill"
                    placeholder="Ingrese número">
            </div>

            <!-- Fecha -->
            <div class="col-lg-3 col-md-3 col-sm-4 col-12">
                <label class="form-label fw-bold">
                    <i class="fa-solid fa-calendar-day text-secondary me-1"></i>
                    Fecha de emisión
                </label>
                <input type="date" name="date" id="date" class="form-control" value="{{ $date }}"
                    readonly>
            </div>

            <!-- Método Pago -->
            <div class="col-lg-3 col-md-3 col-sm-4 col-12">
                <label class="form-label fw-bold">
                    <i class="fa-solid fa-credit-card text-secondary me-1"></i>
                    Método de pago
                </label>
                <select name="payment_method_id" id="payment_method_id" class="form-select">
                    @foreach ($payment_methods as $payment_method)
                        <option value="{{ $payment_method->id }}">
                            {{ $payment_method->description }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        <!-- SEGUNDA FILA -->
        <div class="row g-4 mt-1">

            <!-- Proveedor -->
            <div class="col-md-6">
                <label class="form-label fw-bold">
                    <i class="fa-solid fa-truck-field text-secondary me-1"></i>
                    Proveedor
                </label>
                <select required name="supplier_id" id="supplier_id" class="form-select">
                    <option value=""></option>
                </select>
            </div>

            <!-- Centro Costos -->
            <div class="col-md-3">
                <label class="form-label fw-bold">
                    <i class="fa-solid fa-sitemap text-secondary me-1"></i>
                    Centro de costos
                </label>
                <div class="input-group">
                    <select name="cost_center" id="cost_center" class="form-select" data-placeholder="Seleccionar">
                        <option value=""></option>
                        @foreach ($cost_center as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                    <button class="btn btn-warning" type="button" onclick="openMdlCostCenter()">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
            </div>

        </div>

        <!-- DETALLES -->
        <div class="mt-5">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">
                    <i class="fa-solid fa-list-check text-secondary me-1"></i>
                    Detalle del egreso
                </h6>

                <button type="button" class="btn btn-sm btn-primary px-3" onclick="addRow()">
                    <i class="fa-solid fa-plus me-1"></i> Agregar
                </button>
            </div>

            <div class="table-responsive">
                <table id="egreso-detail" class="table-bordered table-hover table align-middle">

                    <thead class="table-light text-center">
                        <tr>
                            <th width="8%">#</th>
                            <th>Descripción</th>
                            <th width="20%">Total</th>
                            <th width="8%"></th>
                        </tr>
                    </thead>

                    <tbody class="body-table">
                        <tr>
                            <td class="text-center">1</td>

                            <td>
                                <input type="text" name="description[]" class="form-control"
                                    placeholder="Descripción del gasto" oninput="this.value = this.value.toUpperCase()">
                            </td>

                            <td>
                                <input type="text" name="total[]" class="form-control text-end" placeholder="0.00"
                                    oninput="calcularTotalEgreso()">
                            </td>

                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- TOTAL -->
            <div class="bg-light rounded-3 mt-4 border p-4 text-end">
                <span class="text-muted fw-semibold me-2">
                    <i class="fa-solid fa-calculator me-1"></i>
                    Total acumulado:
                </span>
                <span class="fs-4 fw-bold text-primary">
                    S/ <span id="total-del-egreso">0.00</span>
                </span>
            </div>

        </div>

    </div>

</form>
