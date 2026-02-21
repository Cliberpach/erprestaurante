import { elementsUI } from "./state";

export function paintCardsDetail(detalle) {
    const container = document.getElementById('cards_dishes');
    container.innerHTML = '';

    detalle.forEach((item,index) => {
        container.innerHTML += `
            <div class="card mb-2 border-0 shadow-sm" style="height:auto;">
                <div class="card-body py-2">

                    <!-- HEADER -->
                    <div class="d-flex justify-content-between align-items-start mb-2">

                        <!-- NAME + OBSERVATION -->
                        <div class="me-2">
                            <h6 class="fw-bold mb-0">${item.name}</h6>

                            ${
                                item.observation
                                    ? `<small class="text-muted fst-italic">${item.observation}</small>`
                                    : ''
                            }
                        </div>

                        <!-- ACTIONS -->
                        <div class="d-flex gap-1">
                            <button class="btn btn-info btn-sm btnEditItem"
                                type="button"
                                data-producto-id="${item.id}"
                                data-index="${index}">
                                <i class="fas fa-edit"></i>
                            </button>

                            <button class="btn btn-danger btn-sm btnDeleteItem"
                                type="button"
                                data-producto-id="${item.id}"
                                data-index="${index}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>

                    <!-- LINEA UNICA: CANTIDAD - PRECIO - TOTAL -->
                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <small class="text-muted">Cant.</small>
                            <div class="fw-semibold">${item.quantity}</div>
                        </div>

                        <div>
                            <small class="text-muted">Precio</small>
                            <div>S/. ${parseFloat(item.sale_price).toFixed(2)}</div>
                        </div>

                        <div class="text-end bg-light rounded px-2 py-1">
                            <small class="text-muted">Total</small>
                            <div class="fw-bold text-primary">
                                S/. ${parseFloat(item.total).toFixed(2)}
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        `;
    });
}

export function clearFormAddItem(itemSelected) {

    elementsUI.inputProduct.value = '';
    //elementsUI.inputPurchasePrice.value = '';
    elementsUI.inputSalePrice.value = '';
    elementsUI.inputQuantity.value = '';
    elementsUI.inputStock.value = '';
    elementsUI.inputObservation.value = '';

    itemSelected.id = null;
    itemSelected.name = null;
    itemSelected.type_name = null;
    itemSelected.quantity = null;
    itemSelected.type_item = null;
    itemSelected.total = null;

    clearDishSelected();
    clearProductSelected();
}

window.paintCardsDetail = paintCardsDetail;
