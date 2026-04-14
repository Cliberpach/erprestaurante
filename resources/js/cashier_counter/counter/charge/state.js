export let lstDetail = [];
export let lstDetailCanceled = [];
export let dtDetail = null;

export const amounts = {
    subTotal: 0,
    tax: 0,
    total: 0,
    discount: 0,
    totalPay: 0
}

export let filterStatusSelect = null;

export function setLstDetail(instance) {
    lstDetail = instance;
}
export function setLstDetailCanceled(instance) {
    lstDetailCanceled = instance;
}
export function setDtDetail(instance) {
    dtDetail = instance;
}

export function setFilterStatusSelect(instance) {
    filterStatusSelect = instance;
}

export function getAmounts() {
    return amounts;
}
