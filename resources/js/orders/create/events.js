import { eventsUtilChange } from "../shared/events";
import { actionAddItem, actionDeleteItem, actionFormStore, actionPaymentMethodsChange } from "./action";

export function events() {
    app.init();
    eventsUtilChange();
    eventsSubmit();
    eventsClick();
    eventsChange();
}

function eventsSubmit() {
    document.querySelector('#form_create').addEventListener('submit', (e) => {
        e.preventDefault();
        actionFormStore(e.target);
    })
}

function eventsClick() {
    document.addEventListener('click', (e) => {

        if (e.target.closest('.btnAgregarProducto')) {
            actionAddItem();
        }

        const btnDelete = e.target.closest('.btnDeleteItem');
        if (btnDelete) {
            actionDeleteItem(btnDelete);
        }

    })

    document.querySelector('.btn-view-qr').addEventListener('click', (e) => {
        actionPaymentMethodsChange();
    })
}

function eventsChange() {
    window.paymentMethodsSelect.on('change', () => actionPaymentMethodsChange());
}


