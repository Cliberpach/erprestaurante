import { dtAlerts, lstAlertsSelected, setLstAlertsSelected } from "./state";
import { syncRowStyle, updateNotifCounters } from "./ui";

export function isSelected(id) {
    return lstAlertsSelected.some(a => a.id == id);
}

export function bindRowEvents() {
    document.querySelectorAll('#notif-tbody .notif-row').forEach(function (row) {
        // evitar doble bind
        if (row.dataset.bound === '1') return;
        row.dataset.bound = '1';

        // Recuperar data de DataTable para esta fila
        const rowData = dtAlerts.row(row).data();
        if (!rowData) return;

        // Reflejar estado actual si ya estaba seleccionado
        if (isSelected(rowData.id)) syncRowStyle(row, true);

        // Clic en fila o en checkbox
        row.addEventListener('click', function (e) {
            const chk = row.querySelector('.notif-chk');
            const newState = !isSelected(rowData.id);

            if (newState) {
                addAlert(rowData);
            } else {
                removeAlert(rowData.id);
            }

            syncRowStyle(row, newState);
            updateNotifCounters(lstAlertsSelected);

            // sincronizar checkbox cabecera
            const total = document.querySelectorAll('#notif-tbody .notif-row').length;
            const marked = document.querySelectorAll('#notif-tbody .notif-row.notif-selected').length;
            const chkAll = document.getElementById('chk-all-notif');
            if (chkAll) chkAll.checked = total > 0 && total === marked;
        });
    });
}

export function bindHeaderCheckbox() {
    const chkAll = document.getElementById('chk-all-notif');
    if (!chkAll) return;

    chkAll.addEventListener('change', function () {
        const checked = this.checked;
        document.querySelectorAll('#notif-tbody .notif-row').forEach(function (row) {
            const rowData = dtAlerts.row(row).data();
            if (!rowData) return;
            if (checked) {
                addAlert(rowData);
            } else {
                removeAlert(rowData.id);
            }
            syncRowStyle(row, checked);
        });
        updateNotifCounters(lstAlertsSelected);
    });
}

function addAlert(data) {
    if (!isSelected(data.id)) {
        lstAlertsSelected.push(data);
    }
}

function removeAlert(id) {
    const instance = lstAlertsSelected.filter(a => a.id != id);
    setLstAlertsSelected(instance);
}


export function actionBtnClearAlertsSelected(){
    setLstAlertsSelected([]);

    document.querySelectorAll('#notif-tbody .notif-row').forEach(function (row) {
        syncRowStyle(row, false);
    });

    updateNotifCounters(lstAlertsSelected);
}
