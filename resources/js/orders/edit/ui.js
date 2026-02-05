export function paintTblDetail(lstItems) {
    let filas = ``;

    lstItems.forEach((item,index) => {

        const rowClass = !item.order_detail_id ? 'row-new-item' : '';

        filas += `
            <tr class="${rowClass}">
                <th>
                    <div class="d-flex justify-content-center gap-1">
                        <button class="btn btn-danger btn-sm btnDeleteItem" type="button"
                            data-producto-id="${item.id}" data-index="${index}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </th>
                <td>${item.name}</td>
                <td>${formatSoles(item.sale_price)}</td>
                <td>${item.quantity}</td>
                <td>${formatSoles(item.total)}</td>
                <td>${formatSoles(item.purchase_price)}</td>
            </tr>
        `;
    });

    const tbody = document.querySelector('#tbl_order_detail tbody');
    tbody.innerHTML = filas;
}


export function paintAmounts(amounts) {
    document.querySelector('#subtotal_amount').innerText = formatSoles(amounts.subTotal);
    document.querySelector('#igv_amount').innerText = formatSoles(amounts.tax);
    document.querySelector('#total_amount').innerText = formatSoles(amounts.totalPay);
}

window.paintTblDetail   =   paintTblDetail;
