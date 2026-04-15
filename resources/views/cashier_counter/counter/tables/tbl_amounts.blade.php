<table id="tbl-amounts" class="table-bordered table-striped mt-3 table shadow-sm"
    style="text-transform: uppercase; font-size: 14px;">
    <tbody>
        <tr>
            <th style="width: 50%;">
                <i class="fas fa-money-bill-wave text-secondary me-2"></i> SUBTOTAL
            </th>
            <td class="fw-bold text-end" id="subtotal_amount">
                S/ 0.00
            </td>
        </tr>
        <tr>
            <th style="width: 50%;">
                <i class="fas fa-receipt text-warning me-2"></i> IGV (18%)
            </th>
            <td class="fw-bold text-end" id="igv_amount">
                S/ 0.00
            </td>
        </tr>
        <tr class="text-white tr-total">
            <th class="fw-bold" style="widht:50%;">
                <i class="fas fa-coins me-2"></i> TOTAL
            </th>
            <td class="fw-bold fs-5 text-end" id="total_amount">
                S/ 0.00
            </td>
        </tr>
        <tr class="tr-discount">
            <th>
                <i class="fas fa-tags text-danger me-2"></i>
                DCTO (incluye IGV)
            </th>
            <td class="text-end">

                <div style="display:flex; gap:6px; justify-content:end;">

                    <input type="number" step="0.01" min="0" id="discount"
                        class="form-control form-control-sm text-danger fw-bold text-end" value="0.00" readonly
                        style="max-width:120px;">

                    <button type="button" class="btn btn-sm btn-outline-secondary btn-discount"
                        title="Editar descuento">
                        <i class="fas fa-key"></i>
                    </button>

                </div>

            </td>
        </tr>
        <tr class="text-white">
            <th class="fw-bold" style="widht:50%;">
                <i class="fas fa-coins me-2"></i> TOTAL A PAGAR
            </th>
            <td class="fw-bold fs-5 text-end" id="total_pay_amount">
                S/ 0.00
            </td>
        </tr>
    </tbody>
</table>
