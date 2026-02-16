import { actionChangeStatus, actionChargeCreate } from "./action";
import { filterStatusSelect } from "./state";

export function eventsCCounter() {
    eventsClick();
    eventsChange();
}

function eventsClick() {
    document.querySelector('.btn-charge-create').addEventListener('click', actionChargeCreate);
}

function eventsChange() {
    filterStatusSelect.on('change', function (value) {
        actionChangeStatus(value);
    });
}
