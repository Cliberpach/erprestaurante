<form id="formEditCompanyTenant" action="{{ route('tenant.mantenimiento.empresas.update', $company->id) }}" method="POST"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <!-- DATOS GENERALES -->
    <div class="row mb-3">

        <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
            <label for="ruc" class="form-label fw-bold">
                <i class="fas fa-id-card text-primary me-1"></i> RUC
            </label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                <input type="text" class="form-control" id="ruc" name="ruc" value="{{ $company->ruc }}"
                    readonly>
            </div>
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
            <label for="business_name" class="form-label fw-bold">
                <i class="fas fa-building text-primary me-1"></i> Razón Social
            </label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-building"></i></span>
                <input type="text" class="form-control input-fill" id="business_name" name="business_name"
                    value="{{ $company->business_name }}">
            </div>
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
            <label class="form-label fw-bold">
                <i class="fas fa-file-signature text-primary me-1"></i> Razón Social Abreviada
            </label>
            <input type="text" class="form-control input-fill" name="abbreviated_business_name"
                value="{{ $company->abbreviated_business_name }}">
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
            <label class="form-label fw-bold">
                <i class="fas fa-map-marker-alt text-primary me-1"></i> Dirección Fiscal
            </label>
            <input type="text" class="form-control input-fill" name="fiscal_address"
                value="{{ $company->fiscal_address }}">
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
            <label class="form-label fw-bold">
                <i class="fas fa-phone text-primary me-1"></i> Teléfono
            </label>
            <input type="text" class="form-control input-fill" name="phone" value="{{ $company->phone }}">
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
            <label class="form-label fw-bold">
                <i class="fas fa-mobile-alt text-primary me-1"></i> Celular
            </label>
            <input type="text" class="form-control input-fill" name="cellphone" value="{{ $company->cellphone }}">
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
            <label class="form-label fw-bold">
                <i class="fas fa-mailbox text-primary me-1"></i> Código Postal
            </label>
            <input type="text" class="form-control input-fill" name="zip_code" value="{{ $company->zip_code }}">
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
            <label class="form-label fw-bold">
                <i class="fas fa-envelope text-primary me-1"></i> Correo Electrónico
            </label>
            <input type="email" class="form-control input-fill" name="email" value="{{ $company->email }}">
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
            <label class="form-label fw-bold">
                <i class="fab fa-facebook text-primary me-1"></i> Facebook
            </label>
            <input type="text" class="form-control input-fill" name="facebook" value="{{ $company->facebook }}">
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
            <label class="form-label fw-bold">
                <i class="fab fa-instagram text-primary me-1"></i> Instagram
            </label>
            <input type="text" class="form-control input-fill" name="instagram" value="{{ $company->instagram }}">
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
            <label class="form-label fw-bold">
                <i class="fas fa-globe text-primary me-1"></i> Página Web
            </label>
            <input type="text" class="form-control input-fill" name="web" value="{{ $company->web }}">
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
            <label class="form-label fw-bold">
                <i class="fas fa-file-invoice-dollar text-primary me-1"></i> Estado de Facturación
            </label>
            <select class="form-select" name="invoicing_status">
                <option value="0" {{ $company->invoicing_status == '0' ? 'selected' : '' }}>Inactivo</option>
                <option value="1" {{ $company->invoicing_status == '1' ? 'selected' : '' }}>Activo</option>
            </select>
        </div>

    </div>

    <div class="row">
        <div class="col-6">
            <!-- UBICACIÓN -->
            <div class="mb-3">
                <label class="form-label fw-bold">
                    <i class="fas fa-map-marked-alt text-primary me-1"></i> Ubicación
                </label>

                <input id="searchBox" type="text" class="form-control mb-2" placeholder="Buscar dirección...">

                <div id="map" class="rounded border" style="width:100%;height:300px;"></div>

                <input type="hidden" id="lat" name="lat">
                <input type="hidden" id="lng" name="lng">
            </div>
        </div>
        <div class="col-6">
            <label class="form-label fw-bold">
                <i class="fas fa-image text-primary me-1"></i> Logo
            </label>
            <input class="form-control" type="file" name="logo" id="input-logo"
                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
        </div>

    </div>

    <!-- BOTONES -->
    <div class="d-flex justify-content-end gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i> Guardar Cambios
        </button>
        <a href="{{ route('tenant.mantenimiento.empresas.index') }}" class="btn btn-secondary">
            <i class="fas fa-times me-1"></i> Cancelar
        </a>
    </div>

</form>
