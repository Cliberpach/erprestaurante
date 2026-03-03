<form action="" id="formRegisterAccount" method="POST">
    <div class="row">
        @csrf

        <div class="col-12 mb-3">
            <label for="holder" class="form-label fw-bold required_field">Titular</label>
            <div class="input-group">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fas fa-user"></i>
                </span>
                <input maxlength="160" required id="holder" name="holder" type="text" class="form-control input-fill"
                    placeholder="Titular" aria-label="Holder" aria-describedby="basic-addon1">
            </div>
            <span class="holder_error msgError text-danger"></span>
        </div>

        <div class="col-12 mb-3">
            <label for="currency" class="form-label fw-bold required_field">Moneda</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-dollar-sign"></i>
                </span>
                <select name="currency" id="currency" class="select2_account form-select input-fill">
                    <option value="SOLES">SOLES</option>
                    <option value="DOLARES">DÓLARES</option>
                </select>
            </div>
            <span class="currency_error msgError text-danger"></span>
        </div>

        <div class="col-12 mb-3">
            <label for="account_number" class="form-label fw-bold required_field">N° Cuenta</label>
            <div class="input-group">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fas fa-university"></i>
                </span>
                <input maxlength="160" required id="account_number" name="account_number" type="text"
                    class="form-control input-fill" placeholder="Cuenta" aria-label="Account Number"
                    aria-describedby="basic-addon1">
            </div>
            <span class="account_number_error msgError text-danger"></span>
        </div>

        <div class="col-12 mb-3">
            <label for="cci" class="form-label fw-bold required_field">CCI</label>
            <div class="input-group">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fas fa-exchange-alt"></i>
                </span>
                <input maxlength="100" id="cci" name="cci" type="text" class="form-control input-fill"
                    placeholder="CCI" aria-label="CCI" aria-describedby="basic-addon1">
            </div>
            <span class="cci_error msgError text-danger"></span>
        </div>

        <div class="col-12 mb-3">
            <label for="phone" class="form-label fw-bold required_field">Celular</label>
            <div class="input-group">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fas fa-mobile-alt"></i>
                </span>
                <input maxlength="100" id="phone" name="phone" type="text" class="form-control input-fill"
                    placeholder="Celular" aria-label="Phone" aria-describedby="basic-addon1">
            </div>
            <span class="phone_error msgError text-danger"></span>
        </div>

        <div class="col-12 mb-3">
            <label for="bank_id" class="form-label fw-bold required_field">Banco</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-building"></i>
                </span>
                <select name="bank_id" id="bank_id" class="select2_account form-select">
                    @foreach ($banks as $bank)
                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                    @endforeach
                </select>
            </div>
            <span class="bank_id_error msgError text-danger"></span>
        </div>

        <div class="col-12 mb-3">
            <label class="form-label fw-bold">
                <i class="fas fa-qrcode text-success me-1"></i> QR Pago
            </label>

            <input type="file" class="form-control" name="qr" id="qr"
                accept=".jpg,.jpeg,.png,image/jpeg,image/png">

            <small class="text-secondary fst-italic">
                Máx. 4 MB — JPG / JPEG / PNG
            </small>
            <span class="qr_error msgError text-danger"></span>
        </div>

    </div>
</form>
