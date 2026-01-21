<table class="table-hover table-striped table" id="tbl_asignar_cuentas">
    <thead>
        <tr>
            <th scope="col">Asignar</th>
            <th scope="col">Activar</th>
            <th scope="col">Banco</th>
            <th scope="col">N° Cuenta</th>
            <th scope="col">CCI</th>
            <th scope="col">Celular</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($cuentas as $cuenta)
            @php
                $tipo_pago_cuenta = $cuentas_asignadas->first(function ($cuenta_asignada) use ($cuenta) {
                    return (int) $cuenta_asignada->bank_account_id === (int) $cuenta->id;
                });
            @endphp
            <tr>
                <td>
                    <div class="form-check mb-2">
                        <input class="form-check-input check-primary chk-cuenta" type="checkbox"
                            id="chk-cuenta-{{ $cuenta->id }}" data-id="{{ $cuenta->id }}"
                            @if ($tipo_pago_cuenta) checked @endif>
                        <label class="form-check-label" for="chk-cuenta-{{ $cuenta->id }}">Asignar </label>
                    </div>
                </td>
                <td>
                    <div class="form-check form-switch mb-2">
                        <input
                        @if ($tipo_pago_cuenta && $tipo_pago_cuenta->is_active) checked @endif
                        data-id="{{ $cuenta->id }}" class="form-check-input switch-primary chk-activate" type="checkbox"
                            id="chk-activate-{{ $cuenta->id }}">
                        <label class="form-check-label" for="chk-activate-{{ $cuenta->id }}"> Activar </label>
                    </div>
                </td>
                <td>{{ $cuenta->bank_name }}</td>
                <td>{{ $cuenta->account_number }}</td>
                <td>{{ $cuenta->cci }}</td>
                <td>{{ $cuenta->phone }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
