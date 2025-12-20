<form id="form_create_table" method="POST" >
    @csrf
    @method('POST')

    <div class="row g-3">

        <!-- Nombre -->
        <div class="col-12">
            <label for="name" class="form-label fw-bold required_field">Nombre:</label>
            <input
                type="text"
                class="form-control"
                id="name"
                name="name"
                value="{{ old('name') }}"
                required
            >
            <div class="invalid-feedback">
                Este campo es obligatorio.
            </div>
            <p class="name_error msgError mb-0"></p>
        </div>

    </div>
</form>
