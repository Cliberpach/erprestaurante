import { getAmounts } from "../charge/state";

export const CASH_ID = 1;
export let paymentMethodSelect = null;
export let lstPays = [];
export let change = 0;
export let invoiceId = null;

export let configMdlCharge = {
    getAmounts: null
};

export let infoAmounts = {
    total: 0,        // total del pedido
    paid: 0,         // lo aplicado a la venta
    pending: 0,      // lo que falta pagar
    change: 0        // vuelto (solo efectivo)
};

export function setPaymentMethodSelect(instance) {
    paymentMethodSelect = instance;
}

export function setLstPays(instance) {
    lstPays = instance;
}
export function setChange(instance) {
    change = instance;
}
export function setInvoiceId(instance) {
    invoiceId = instance
}

export function setConfigMdlCharge(instance) {
    configMdlCharge = instance;
}
