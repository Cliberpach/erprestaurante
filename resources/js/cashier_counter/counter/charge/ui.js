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
    document.querySelector('#total_amount').innerText = formatSoles(amounts.total);
    document.querySelector('#total_pay_amount').innerText = formatSoles(amounts.totalPay);
}

export function disabledInputDiscount() {
    const btn = document.querySelector('.btn-discount');
    const container = btn.closest('div');
    const input = container.querySelector('input');

    if (input.hasAttribute('readonly')) {
        input.removeAttribute('readonly');
        input.focus();
        btn.innerHTML = '<i class="fas fa-lock-open"></i>';
    } else {
        input.setAttribute('readonly', true);
        btn.innerHTML = '<i class="fas fa-key"></i>';
    }
}

export function enableDiscount(configDiscount) {
    if (configDiscount == 0) {
        document.querySelector('.tr-discount').remove();
        document.querySelector('.tr-total').remove();
    }
}

export function renderDueDate(nroDays) {
    const col = document.getElementById('due-date-col');
    if (!nroDays || nroDays <= 0) {
        col.classList.add('d-none');
        return;
    }
    col.classList.remove('d-none');
    const date = new Date();
    date.setDate(date.getDate() + nroDays);
    const yyyy = date.getFullYear();
    const mm = String(date.getMonth() + 1).padStart(2, '0');
    const dd = String(date.getDate()).padStart(2, '0');
    document.getElementById('due-date-input').value = `${yyyy}-${mm}-${dd}`;
}

window.paintTblDetail = paintTblDetail;
