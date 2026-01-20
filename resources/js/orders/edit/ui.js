import { elementsUI } from "./state";

export function paintTblDetail(lstItems) {
    let filas = ``;
    lstItems.forEach((item) => {
        filas += `<tr>
                            <th>
                                <div class="d-flex justify-content-center gap-1">

                                    <button class="btn btn-info btn-sm btnEditItem" type="button"
                                    data-producto-id="${item.id}">
                                        <i class="fas fa-edit"></i>
                                    </button>


                                    <button class="btn btn-danger btn-sm btnDeleteItem" type="button"
                                    data-producto-id="${item.id}">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                </div>
                            </th>
                            <td>${item.name}</td>
                            <td>${item.type_item}</td>
                            <td>${item.type_name}</td>
                            <td>${formatSoles(item.sale_price)}</td>
                            <td>${item.quantity}</td>
                            <td>${formatSoles(item.total)}</td>
                            <td>${formatSoles(item.purchase_price)}</td>
                        </tr>`;
    })

    const tbody = document.querySelector('#tbl_order_detail tbody');
    tbody.innerHTML = filas;
}

export function paintAmounts(amounts) {
    document.querySelector('#subtotal_amount').innerText = formatSoles(amounts.subTotal);
    document.querySelector('#igv_amount').innerText = formatSoles(amounts.tax);
    document.querySelector('#total_amount').innerText = formatSoles(amounts.totalPay);
}

export function clearFormAddItem(itemSelected) {

    elementsUI.inputProduct.value = '';
    elementsUI.inputPurchasePrice.value = '';
    elementsUI.inputSalePrice.value = '';
    elementsUI.inputQuantity.value = '';
    elementsUI.inputStock.value = '';

    itemSelected.id = null;
    itemSelected.name = null;
    itemSelected.type_name = null;
    itemSelected.quantity = null;
    itemSelected.type_item = null;
    itemSelected.total = null;

    clearDishSelected();
    clearProductSelected();
}

window.paintTblDetail   =   paintTblDetail;
