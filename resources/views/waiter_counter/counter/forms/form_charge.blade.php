<form action="" id="formCharge">
    <!-- DATOS DEL PEDIDO: código, mesa y total -->
    <div class="d-flex align-items-center mb-3 flex-wrap gap-2">
        <span class="badge text-bg-light fs-6 border">
            <i class="fas fa-hashtag text-muted me-1"></i>
            <span id="spanOrderCode">—</span>
        </span>
        <span class="badge text-bg-light fs-6 border">
            <i class="fas fa-chair text-muted me-1"></i>Mesa
            <span id="spanTableName" class="fw-bold">—</span>
        </span>
        <span class="badge text-bg-primary fs-6 ms-auto">
            <i class="fas fa-coins me-1"></i>
            S/ <span id="spanTotal">—</span>
        </span>
    </div>

    <!-- QR: prioridad visual en móvil -->
    <div class="mb-3 text-center">
        <p class="text-muted small mb-2">
            <i class="fas fa-mobile-screen me-1"></i>Muestra este QR al cliente para el pago
        </p>

        <div class="div-qr-payment">
            <a class="lg-qr-payment" data-src="">
                <img src="" id="imgQrMdlCharge" style="height:300px;object-fit: cover;">
            </a>
        </div>

    </div>

    <!-- Método de pago -->
    <div class="mb-3">
        <label for="payment_method" class="form-label fw-bold">Método de Pago</label>
        <select required name="payment_method" id="payment_method" class="form-control">
            <option value="">Seleccionar método de pago</option>
            @foreach ($payment_methods as $method)
                <option value="{{ $method->id }}">{{ $method->description }}</option>
            @endforeach
        </select>
        <p class="payment_method_error msgError mb-0"></p>
    </div>

    <!-- Foto del comprobante de pago -->
    <div class="mb-2">
        <label for="inputVoucher" class="form-label fw-semibold">
            <i class="fas fa-camera me-1"></i>Foto del comprobante
        </label>
        <input required type="file" class="form-control" id="inputVoucher" name="voucher" accept="image/*"
            capture="environment">
        <p class="voucher_error msgError mb-0"></p>
    </div>
</form>
