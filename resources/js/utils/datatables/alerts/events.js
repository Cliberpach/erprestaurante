import { actionBtnClearAlertsSelected } from "./actions";

export function eventsDtAlerts() {
    document.getElementById('btn-clear-notif').addEventListener('click', function () {
        actionBtnClearAlertsSelected();
    });
}
