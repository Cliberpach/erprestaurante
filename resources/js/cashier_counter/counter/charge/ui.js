export function paintTblDetail(lstItems, lstItemsCanceled) {
    let filas = ``;
    lstItems.forEach((item) => {
        filas += `<tr>
                    <td>ACTIVO</td>
                    <td>${item.quantity}</td>
                    <td>${item.name}</td>
                    <td>${item.type_name}</td>
                    <td>${formatSoles(item.sale_price)}</td>
                    <td>${formatSoles(item.total)}</td>
                </tr>`;
    })

    lstItemsCanceled.forEach((item) => {
        filas += `<tr class="item-canceled">
                    <td>ELIMINADO</td>
                    <td>${item.quantity}</td>
                    <td>${item.name}</td>
                    <td>${item.type_name}</td>
                    <td>${formatSoles(item.sale_price)}</td>
                    <td>${formatSoles(item.total)}</td>
                </tr>`;
    })

    const tbody = document.querySelector('#dt-detail tbody');
    tbody.innerHTML = filas;
}

export function paintAmounts(amounts) {
    document.querySelector('#subtotal_amount').innerText = formatSoles(amounts.subTotal);
    document.querySelector('#igv_amount').innerText = formatSoles(amounts.tax);
    document.querySelector('#total_amount').innerText = formatSoles(amounts.totalPay);
}

window.paintTblDetail = paintTblDetail;
