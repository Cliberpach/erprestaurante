import { eventsUtilChange } from "../shared/events";
import { actionAddItem, actionDeleteItem, actionFormUpdate } from "./action";

export function events() {
    app.init();
    eventsUtilChange();
    eventsSubmit();
    eventsClick();
}

function eventsSubmit() {
    document.querySelector('#form_edit').addEventListener('submit', (e) => {
        e.preventDefault();
        actionFormUpdate(e.target);
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
}
