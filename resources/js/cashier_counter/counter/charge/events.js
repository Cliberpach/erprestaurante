import { actionChargeCreate } from "./action";

export function eventsCCounter() {
    eventsClick();
}

function eventsClick() {
    document.querySelector('.btn-charge-create').addEventListener('click', actionChargeCreate);
}
