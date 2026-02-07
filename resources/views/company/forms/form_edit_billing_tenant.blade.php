<form action="" method="post" id="form_edit_billing" enctype="multipart/form-data">
    @csrf

    <div class="row g-3">

        <!-- URBANIZACIÓN -->
        <div class="col-lg-6 col-12">
            <label class="fw-bold required_field" for="urbanization">URBANIZACIÓN</label>
            <div class="input-group">
                <span class="input-group-text text-secondary">
                    <i class="fas fa-city"></i>
                </span>
                <input value="{{ $company_invoice->urbanization }}" required id="urbanization" name="urbanization"
                    type="text" class="form-control input-fill" placeholder="URBANIZACIÓN">
            </div>
            <p class="urbanization_error msgError"></p>
        </div>

        <!-- CÓDIGO LOCAL -->
        <div class="col-lg-6 col-12">
            <label class="fw-bold required_field" for="local_code">CÓDIGO DE LOCAL</label>
            <div class="input-group">
                <span class="input-group-text text-secondary">
                    <i class="fas fa-industry"></i>
                </span>
                <input value="{{ $company_invoice->local_code }}" required id="local_code" name="local_code"
                    type="text" class="form-control input-fill" placeholder="CÓDIGO DE LOCAL">
            </div>
            <p class="local_code_error msgError"></p>
        </div>

        <!-- USUARIO SOL -->
        <div class="col-lg-6 col-12">
            <label class="fw-bold required_field" for="sol_user">USUARIO SOL</label>
            <div class="input-group">
                <span class="input-group-text text-primary">
                    <i class="fas fa-user-lock"></i>
                </span>
                <input value="{{ $company_invoice->secondary_user }}" required id="sol_user" name="sol_user"
                    type="text" class="form-control input-fill" placeholder="USUARIO SOL">
            </div>
            <p class="sol_user_error msgError"></p>
        </div>

        <!-- CLAVE SOL -->
        <div class="col-lg-6 col-12">
            <label class="fw-bold required_field" for="sol_pass">CLAVE SOL</label>
            <div class="input-group">
                <span class="input-group-text text-success">
                    <i class="fas fa-key"></i>
                </span>
                <input value="{{ $company_invoice->secondary_password }}" required id="sol_pass" name="sol_pass"
                    type="text" class="form-control input-fill" placeholder="CLAVE SOL">
            </div>
            <p class="sol_pass_error msgError"></p>
        </div>

        <!-- USUARIO GRE -->
        <div class="col-lg-6 col-12">
            <label class="fw-bold" for="api_user_gre">USUARIO GUÍAS REMISIÓN</label>
            <div class="input-group">
                <span class="input-group-text text-primary">
                    <i class="fas fa-user-lock"></i>
                </span>
                <input value="{{ $company_invoice->api_user_gre }}" id="api_user_gre" name="api_user_gre" type="text"
                    class="form-control input-fill" placeholder="USUARIO GRE">
            </div>
            <p class="api_user_gre_error msgError"></p>
        </div>

        <!-- CLAVE GRE -->
        <div class="col-lg-6 col-12">
            <label class="fw-bold" for="api_pass_gre">CLAVE GUÍAS REMISIÓN</label>
            <div class="input-group">
                <span class="input-group-text text-success">
                    <i class="fas fa-key"></i>
                </span>
                <input value="{{ $company_invoice->api_password_gre }}" id="api_pass_gre" name="api_pass_gre"
                    type="text" class="form-control input-fill" placeholder="CLAVE GRE">
            </div>
            <p class="api_pass_gre_error msgError"></p>
        </div>

        <!-- CERTIFICADO -->
        <div class="col-lg-6 col-12">
            <label class="fw-bold required_field" for="certificate">CERTIFICADO DIGITAL</label>

            <input accept=".pem,.p12" class="form-control input-fill" id="certificate" name="certificate"
                type="file">

            <div class="text-dark mt-1">
                @if ($company_invoice->certificate)
                    {{ $company_invoice->certificate }}
                @else
                    SIN CERTIFICADO
                @endif
            </div>

            <small class="text-primary fst-italic">
                Formatos permitidos: <b>.PEM</b> o <b>.P12</b>.
                Si sube <b>.P12</b>, se convertirá automáticamente a <b>.PEM</b>.
            </small>

            <p class="certificate_error msgError"></p>
        </div>

        <!-- PASSWORD CERTIFICADO -->
        <div class="col-lg-6 col-12">
            <label class="fw-bold" for="certificate_password">CLAVE DEL CERTIFICADO</label>
            <div class="input-group">
                <span class="input-group-text text-warning">
                    <i class="fas fa-lock"></i>
                </span>
                <input type="text" class="form-control input-fill" id="certificate_password" maxlength="100"
                    name="certificate_password" placeholder="Contraseña del certificado (.P12)"
                     value="{{ $company_invoice->certificate_password }}">
            </div>
            <small class="text-muted fst-italic">
                Obligatorio solo si el certificado es <b>.P12</b>.
            </small>
            <p class="certificate_password_error msgError"></p>
        </div>

    </div>
</form>
