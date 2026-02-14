export let lstDetail = [];
export let lstDetailCanceled = [];
export const amounts = {
    subTotal: 0,
    tax: 0,
    totalPay: 0
}
export function setLstDetail(instance) {
    lstDetail = instance;
}
export function setLstDetailCanceled(instance) {
    lstDetailCanceled = instance;
}

