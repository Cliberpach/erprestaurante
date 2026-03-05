export let dtAlerts = null;
export let lstAlertsSelected = [];

export function setDtAlerts(instance) {
    dtAlerts = instance;
}

export function getDtAlerts() {
    return dtAlerts;
}

export function getLstAlertsSelected() {
    return lstAlertsSelected;
}

export function setLstAlertsSelected(instance) {
    lstAlertsSelected = instance;
}
