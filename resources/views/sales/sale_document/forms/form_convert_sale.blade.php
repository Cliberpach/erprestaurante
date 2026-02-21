<form action="" id="form-convert-sale">
    <div class="row g-3">

        <!-- Tipo comprobante -->
        <div class="col-12">
            <label for="invoice_id_mc" class="form-label fw-semibold">
                <i class="fas fa-file-invoice text-primary me-1"></i>
                Tipo comprobante
            </label>

            <select class="form-select" id="invoice_id_mc" name="invoice_id_mc" required>
                @foreach ($invoice_types as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
            </select>

            <small class="invoice_id_mc_error msgError text-danger"></small>
        </div>

        <!-- Cliente -->
        <div class="col-12">

            <div class="mb-3">
                <label for="customer_id_mc" class="form-label fw-semibold">
                    <i class="fas fa-user-tag text-primary me-1"></i>
                    Cliente
                </label>
                <i class="fas fa-plus btn btn-warning btn-sm" onclick="openMdlNewCustomer();"
                    style="margin-left:4px;"></i>
            </div>

            <select class="form-select" name="customer_id_mc" id="customer_id_mc">
            </select>
        </div>

    </div>
</form>
