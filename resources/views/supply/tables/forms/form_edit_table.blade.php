<form method="POST" id="form_edit_color">
    @csrf

    <div class="row g-3">
        <div class="col-12">
            <label for="name_edit" class="form-label fw-bold required_field">Nombre:</label>
            <input type="text" class="form-control" name="name_edit" id="name_edit"
                value="{{ old('name_edit') }}" required>
            <div class="invalid-feedback">
                Por favor, ingresa una descripción válida.
            </div>
            <p class="name_edit_error msgError text-danger small mt-1"></p>
        </div>
    </div>
</form>
