<form id="form_create" method="POST">
    @csrf
    @method('POST')

    <p class="text-muted mb-2">
        <span class="text-danger">*</span> Los campos marcados son obligatorios.
    </p>
    <div class="row g-3">

        <!-- Tipo Plato -->
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
            <label for="type_dish_id" class="form-label fw-bold required_field">TIPO PLATO:</label><i
                class="fas fa-add btn btn-warning" onclick="openMdlNewCustomer();"
                style="margin-left:4px;margin-bottom:4px;"></i>
            <select class="form-control" id="type_dish_id" name="type_dish_id" required>
                <option value="">Seleccionar</option>
                @foreach ($types_dish as $type_dish)
                    <option value="{{$type_dish->id}}">{{$type_dish->name}}</option>
                @endforeach
            </select>
            <div class="invalid-feedback">
                Debe seleccionar un tipo de plato.
            </div>
            <p class="type_dish_id_error msgError mb-0"></p>
        </div>

        <!-- Nombre -->
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
            <label for="name" class="form-label fw-bold required_field">Nombre:</label>
            <div class="input-group">
                <input type="text" class="form-control text-uppercase" id="name" name="name" maxlength="160"
                    placeholder="Ingrese un nombre" required>
            </div>
            <p class="name_error msgError mb-0"></p>
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
            <label for="purchase_price" class="form-label fw-bold required_field">Precio Compra:</label>
            <div class="input-group">
                <input type="text" value="0" class="form-control text-uppercase inputDecimalPositivo"
                    id="purchase_price" name="purchase_price" placeholder="Ingrese un precio compra" required>
            </div>
            <p class="purchase_price_error msgError mb-0"></p>
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
            <label for="sale_price" class="form-label fw-bold required_field">Precio Venta:</label>
            <div class="input-group">
                <input type="text" class="form-control text-uppercase inputDecimalPositivo" id="sale_price"
                    name="sale_price" placeholder="Ingrese un precio venta" required>
            </div>
            <p class="sale_price_error msgError mb-0"></p>
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
            <label class="form-label small fw-bold">Imagen</label>
            <input accept=".jpg,.jpeg,.png,.webp,.avif,image/jpeg,image/png,image/webp,image/avif" data-max-files="1"
                data-allow-reorder="true" data-max-file-size="3MB" type="file" name="img" accept="image/*"
                class="filepond-input form-control">
            <p class="img_error msgError mb-0"></p>
        </div>

    </div>
</form>
