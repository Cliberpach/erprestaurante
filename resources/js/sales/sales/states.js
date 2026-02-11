export const lstSale = [];

export let dtProducts = [];

export const amounts = {
    subtotal: 0,
    monto_igv: 0,
    total: 0
};

export const lstPays = [{
    method_pay: 1,
    amount: 0
}];

export let debounceTimer;

export const app = window.app;
export let lastCustomerQuery = null;
export let lastVehicleQuery = null;
export let customerSelect = null;

export function setDtProducts(instance) {
    dtProducts = instance;
}

export function setDebounceTimer(instance) {
    debounceTimer = instance;
}

export function setLastCustomerQuery(instance) {
    lastCustomerQuery = instance;
}
export function getLastCustomerQuery() {
    return lastCustomerQuery;
}

export function setLastVehicleQuery(instance) {
    lastVehicleQuery = instance;
}

export function setCustomerSelect(instance) {
    customerSelect = instance;
}

export function getCustomerSelect() {
    return customerSelect;
}

window.getCustomerSelect = getCustomerSelect;
