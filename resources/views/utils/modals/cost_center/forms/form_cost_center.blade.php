<form id="form_cost_center" method="POST">
    @csrf
    @method('POST')

    <div class="row g-3">

        <div class="col-12">
            <label for="name_mdlcostcenter" class="form-label fw-bold required_field">Nombre:</label>
            <input type="text" class="form-control" id="name_mdlcostcenter" name="name_mdlcostcenter" maxlength="500"
                value="{{ old('name') }}" required>
            <div class="invalid-feedback">Este campo es obligatorio.</div>
            <p class="name_mdlcostcenter_error msgError mb-0"></p>
        </div>

    </div>

</form>
