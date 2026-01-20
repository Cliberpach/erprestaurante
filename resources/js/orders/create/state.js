export let dtDetail = null;
export let lstDetail = [];
export let itemSelected = {
    id: null,
    warehouse_id:null,
    programming_id:null,
    name: null,
    type_name: null,
    purchase_price: null,
    sale_price: null,
    type_item: null,
    quantity: null,
    total: null
};
export const amounts = {
    subTotal: 0,
    tax: 0,
    totalPay: 0
}

export const elementsUI = {
    inputQuantity: document.querySelector('#cantidad'),
    inputProduct: document.querySelector('#producto'),
    inputPurchasePrice: document.querySelector('#purchase_price'),
    inputSalePrice: document.querySelector('#sale_price'),
    inputQuantity: document.querySelector('#cantidad'),
    inputStock: document.querySelector('#item_stock')
}

export function setDtDetail(instance) {
    dtDetail = instance;
}
export function setLstDetail(instance) {
    lstDetail = instance;
}
export function getLstDetail() {
    return lstDetail;
}
export function setItemSelected(instance) {
    itemSelected = instance;
}
export function getAmounts() {
    return amounts;
}
export function getDtDetail() {
    return dtDetail;
}

window.setItemSelected = setItemSelected;
window.getLstDetail = getLstDetail;
window.getAmounts = getAmounts;
window.setDtDetail = setDtDetail;
window.getDtDetail = getDtDetail;
