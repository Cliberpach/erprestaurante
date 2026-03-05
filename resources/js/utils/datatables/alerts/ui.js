export function syncRowStyle(row, selected) {
    row.classList.toggle('notif-selected', selected);
    const chk = row.querySelector('.notif-chk');
    if (chk) chk.checked = selected;
}

export function updateNotifCounters(lstAlertsSelected) {
    const count = lstAlertsSelected.length;
    const total = lstAlertsSelected.reduce((sum, a) => sum + parseFloat(a.amount || 0), 0);

    const elCount = document.getElementById('notif-selected-count');
    const elTotal = document.getElementById('notif-total-sel');
    if (elCount) elCount.textContent = count + ' sel.';
    if (elTotal) elTotal.textContent = 'Total: S/ ' + total.toFixed(2);
}
