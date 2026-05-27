import { actionBtnDiscount, actionChangePaymentCondition, actionChangeStatus, actionChargeCreate, actionInputDiscount } from "./action";
import { filterStatusSelect, paymentConditionSelect } from "./state";

export function eventsCCounter() {
    eventsClick();
    eventsChange();
    eventsInput();
}

function eventsClick() {
    document.querySelector('.btn-charge-create').addEventListener('click', actionChargeCreate);

    document.querySelector('#tbl-amounts').addEventListener('click', actionBtnDiscount);
}

function eventsChange() {
    filterStatusSelect.on('change', function (value) {
        actionChangeStatus(value);
    });
    paymentConditionSelect.on('change', actionChangePaymentCondition);
}

function eventsInput() {
    document.querySelector('#discount').addEventListener('input', actionInputDiscount)
}
