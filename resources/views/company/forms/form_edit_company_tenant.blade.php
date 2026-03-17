<form id="formEditCompanyTenant" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

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
            <span class="ruc_error msgError" style="color:red;"></span>
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
            <span class="business_name_error msgError" style="color:red;"></span>
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
            <label class="form-label fw-bold">
                <i class="fas fa-file-signature text-primary me-1"></i> Razón Social Abreviada
            </label>
            <input type="text" class="form-control input-fill" name="abbreviated_business_name"
                value="{{ $company->abbreviated_business_name }}">
            <span class="abbreviated_business_name_error msgError" style="color:red;"></span>
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
            <label class="form-label fw-bold">
                <i class="fas fa-map-marker-alt text-primary me-1"></i> Dirección Fiscal
            </label>
            <input type="text" class="form-control input-fill" name="fiscal_address"
                value="{{ $company->fiscal_address }}">
            <span class="fiscal_address_error msgError" style="color:red;"></span>
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
            <label class="form-label fw-bold">
                <i class="fas fa-phone text-primary me-1"></i> Teléfono
            </label>
            <input type="text" class="form-control input-fill" name="phone" value="{{ $company->phone }}">
            <span class="phone_error msgError" style="color:red;"></span>
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
            <label class="form-label fw-bold">
                <i class="fas fa-mobile-alt text-primary me-1"></i> Celular
            </label>
            <input type="text" class="form-control input-fill" name="cellphone" value="{{ $company->cellphone }}">
            <span class="cellphone_error msgError" style="color:red;"></span>
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
            <label class="form-label fw-bold">
                <i class="fas fa-mailbox text-primary me-1"></i> Código Postal
            </label>
            <input type="text" class="form-control input-fill" name="zip_code" value="{{ $company->zip_code }}">
            <span class="zip_code_error msgError" style="color:red;"></span>
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
            <label class="form-label fw-bold">
                <i class="fas fa-envelope text-primary me-1"></i> Correo Electrónico
            </label>
            <input type="email" class="form-control input-fill" name="email" value="{{ $company->email }}">
            <span class="email_error msgError" style="color:red;"></span>
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
            <label class="form-label fw-bold">
                <i class="fab fa-facebook text-primary me-1"></i> Facebook
            </label>
            <input type="text" class="form-control input-fill" name="facebook" value="{{ $company->facebook }}">
            <span class="facebook_error msgError" style="color:red;"></span>
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
            <label class="form-label fw-bold">
                <i class="fab fa-instagram text-primary me-1"></i> Instagram
            </label>
            <input type="text" class="form-control input-fill" name="instagram" value="{{ $company->instagram }}">
            <span class="instagram_error msgError" style="color:red;"></span>
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
            <label class="form-label fw-bold">
                <i class="fas fa-globe text-primary me-1"></i> Página Web
            </label>
            <input type="text" class="form-control input-fill" name="web" value="{{ $company->web }}">
            <span class="web_error msgError" style="color:red;"></span>
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
            <label class="form-label fw-bold">
                <i class="fas fa-percent text-primary me-1"></i> IGV
            </label>
            <input type="text" class="form-control input-fill inputDecimalPositivo" name="igv"
                value="{{ number_format($company->igv, 2, '.', '') }}" maxlength="4" step="0.01">
            <span class="igv_error msgError" style="color:red;"></span>
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
            <label class="required_field" for="department" style="font-weight: bold;">DEPARTAMENTO</label>
            <select required name="department" required class="" id="department"
                data-placeholder="Seleccionar" onchange="changeDepartment(this.value)">
                <option></option>
                @foreach ($departments as $department)
                    <option @if ($company_invoice->department_id == $department->id) selected @endif value="{{ $department->id }}">
                        {{ $department->name }}</option>
                @endforeach
            </select>
            <span class="department_error msgError" style="color:red;"></span>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
            <label class="required_field" for="province" style="font-weight: bold;">PROVINCIA</label>
            <select required name="province" required class="" id="province" data-placeholder="Seleccionar"
                onchange="changeProvince(this.value)">
                <option></option>
            </select>
            <span class="province_error msgError" style="color:red;"></span>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
            <label class="required_field" for="district" style="font-weight: bold;">DISTRITO</label>
            <select required name="district" required class="" id="district" data-placeholder="Seleccionar">
                <option></option>
            </select>
            <span class="district_error msgError" style="color:red;"></span>
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
            <span class="logo_error msgError" style="color:red;"></span>
        </div>
    </div>

    <!-- BOTONES -->
    <div class="d-flex justify-content-end gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i> Guardar
        </button>
        <a href="{{ route('tenant.mantenimiento.empresas.index') }}" class="btn btn-secondary">
            <i class="fas fa-times me-1"></i> Cancelar
        </a>
    </div>

</form>
