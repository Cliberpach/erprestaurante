export let customerSelect = null;

export function setCustomerSelect(instance) {
    customerSelect = instance;
}

export function getCustomerSelect() {
    return customerSelect;
}

window.getCustomerSelect = getCustomerSelect;
