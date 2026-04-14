export function disabledInputsPayment() {
    const inputs = document.querySelectorAll('.input-payment');
    inputs.forEach((i) => {
        i.disabled = true;
        i.value = 0;
        i.dispatchEvent(new Event('input', { bubbles: true }));
    })
}

export function enabledInputsPayment() {
    const inputs = document.querySelectorAll('.input-payment');
    inputs.forEach((i) => {
        i.disabled = false;
    })
}

export function setDataFormCharge() {
    document.querySelector('#qr-img-preview').src = app.payrefImgUrl;
}

export function paintChange(charge) {
    document.querySelector('.change_mdlCharge').textContent = formatSoles(charge);
}

export function desactiveBtnsInvoice() {
    document.querySelectorAll('.comprobante-btn').forEach(b => b.classList.remove('active'));
}


export function renderSummary(infoAmounts) {
    console.log(infoAmounts,'infoAmounts');
    document.getElementById('summary-total').textContent = formatSoles(infoAmounts.total);
    document.getElementById('summary-paid').textContent = formatSoles(infoAmounts.paid);
    document.getElementById('summary-pending').textContent = formatSoles(infoAmounts.pending);
    document.getElementById('summary-change').textContent = formatSoles(infoAmounts.change);

    // Pendiente: rojo si hay saldo, oculto si está saldado
    const pendingBox = document.getElementById('summary-pending-box');
    const hasPending = infoAmounts.pending > 0;
    pendingBox.classList.toggle('d-none', !hasPending);

    // Vuelto: solo visible si hay vuelto
    const changeBox = document.getElementById('summary-change-box');
    changeBox.classList.toggle('d-none', infoAmounts.change <= 0);
}
