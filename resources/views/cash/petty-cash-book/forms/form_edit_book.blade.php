<form action="" id="form-edit-book">

    <div class="col-12 mb-3">
        <label class="form-label">Caja seleccionada</label>
        <div class="d-flex align-items-center bg-light gap-3 rounded border p-2">
            <!-- Icono de caja -->
            <i class="fa-solid fa-cash-register text-primary fs-3"></i>
            <!-- Información en diferentes líneas -->
            <div>
                <span id="infoCodigoCaja" class="d-block fw-bold text-primary fs-6">
                    CÓDIGO_CAJA
                </span>
                <span id="infoNombreCaja" class="d-block fw-semibold text-secondary fs-6">
                    NOMBRE_CAJA
                </span>
            </div>
        </div>
    </div>

    <div class="col-12 mb-3">
        <label for="selectTurnos" class="form-label required_field">Turno</label>
        <select required name="shift_edit" class="form-select" id="shift_edit" aria-label="Default select example">
            <option value="">Seleccionar</option>
            @foreach ($shiftList as $shift)
                <option value="{{ $shift->id }}">{{ $shift->time }}</option>
            @endforeach
        </select>
        <p class="msgError shift_edit_error"></p>
    </div>

    <div class="col-12 mb-3">
        <label for="initial_amount" class="form-label required_field">Saldo inicial</label>
        <div class="input-group mb-1">
            <span class="input-group-text">S/.</span>
            <input required value="0" type="text" class="form-control inputDecimalPositivo"
                aria-label="Amount (to the nearest dollar)" id="initial_amount_edit" name="initial_amount_edit">

        </div>
        <p class="msgError initial_amount_edit_error"></p>
    </div>

    <div class="col-12 mb-3">
        <label for="servers" class="form-label">MESEROS
            <button class="btn btn-primary btnReloadServersEdit" type="button">
                <i class="fa-solid fa-rotate-right cursor-pointer" id="reloadServersEdit" title="Recargar meseros"></i>
            </button>
        </label>
        @include('cash.petty-cash-book.tables.tbl_servers_edit')
        <p class="msgError servers_edit_error"></p>
    </div>

</form>
