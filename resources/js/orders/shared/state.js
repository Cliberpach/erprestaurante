export const desktopQuery = window.matchMedia('(min-width: 992px)');
export let lastIsDesktop = desktopQuery.matches;

export let lstDetail = [];
export let dtDetail = null;

export let fpVoucher = null;


export const elementsUI = {
    inputQuantity: document.querySelector('#cantidad'),
    inputProduct: document.querySelector('#producto'),
    inputPurchasePrice: document.querySelector('#purchase_price'),
    inputSalePrice: document.querySelector('#sale_price'),
    inputQuantity: document.querySelector('#cantidad'),
    inputStock: document.querySelector('#item_stock'),
    inputObservation: document.querySelector('#observation_item'),
    imgQrPayment: document.querySelector('#img-qr-payment'),
    inputVoucher: document.querySelector('#voucher')
}

export function setLastIsDesktop(instance) {
    lastIsDesktop = instance;
}
export function setLstDetail(instance) {
    lstDetail = instance;
}
export function getLstDetail() {
    return lstDetail;
}
export function setDtDetail(instance) {
    dtDetail = instance;
}
export function getDtDetail() {
    return dtDetail;
}
export function setFpVoucher(instance) {
    fpVoucher = instance;
}
export function getFpVoucher() {
    return fpVoucher;
}

window.getLstDetail = getLstDetail;
window.setDtDetail = setDtDetail;
window.getDtDetail = getDtDetail;
window.setLstDetail = setLstDetail;

