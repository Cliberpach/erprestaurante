let lastCustomerQuery = null;

export let itemSelected = {
    id: null,
    warehouse_id: null,
    programming_id: null,
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

export function setItemSelected(instance) {
    itemSelected = instance;
}
export function getAmounts() {
    return amounts;
}

export function setLastCustomerQuery(instance) {
    lastCustomerQuery = instance;
}

export function getLastCustomerQuery() {
    return lastCustomerQuery;
}

window.setItemSelected = setItemSelected;
window.getAmounts = getAmounts;
