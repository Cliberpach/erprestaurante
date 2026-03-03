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

export function setDataFormCharge(amounts) {
    document.querySelector('#total-mdlcharge').textContent = formatSoles(amounts.totalPay);
    document.querySelector('#qr-img-preview').src = app.payrefImgUrl;
}

export function paintChange(charge) {
    document.querySelector('.change_mdlCharge').textContent = formatSoles(charge);
}

export function desactiveBtnsInvoice() {
    document.querySelectorAll('.comprobante-btn').forEach(b => b.classList.remove('active'));
}
