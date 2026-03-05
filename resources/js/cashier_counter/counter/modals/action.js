import { lstAlertsSelected } from "../../../utils/datatables/alerts/state";
import { customerSelect } from "../../../utils/selects/customers/state";
import { amounts } from "../charge/state";
import { CASH_ID, infoAmounts, invoiceId, lstPays, paymentMethodSelect, setInvoiceId } from "./state";
import { desactiveBtnsInvoice, disabledInputsPayment, enabledInputsPayment, renderSummary, setDataFormCharge } from "./ui";
import { validatePayments } from "./validation";

export function openMdlCharge() {
    setConfigDefault();
    setDataFormCharge();
    $('#mdl_charge').modal('show');
}

export function actionPaymentMethod(value) {
    disabledInputsPayment();
    if (value !== 'MIXTO') {
        const inputPayment = document.querySelector(`.input-payment-${value}`);
        inputPayment.disabled = false;
        inputPayment.value = formatNumber(amounts.totalPay);
        inputPayment.dispatchEvent(new Event('input', { bubbles: true }));
    } else {
        enabledInputsPayment();
    }
}

export function setConfigDefault() {
    paymentMethodSelect.setValue(2);

    const values = Object.keys(customerSelect.options);
    if (values.length > 0) {
        customerSelect.setValue(values[0]);
    }

    let invoiceCard = document.querySelector('#invoice-type-67');

    if (app.order.customer_type_document_abbreviation === 'RUC') {
        invoiceCard = document.querySelector('#invoice-type-66');
    }

    if (app.order.customer_type_document_abbreviation === 'DNI' && app.order.customer_document_number) {
        invoiceCard = document.querySelector('#invoice-type-65');
    }

    if (invoiceCard) {
        invoiceCard.dispatchEvent(new MouseEvent('click', {
            bubbles: true,
            cancelable: true,
            view: window
        }));
    }
}

export function onKeyDownPayment(e) {
    const allowed = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab', 'Enter'];

    // Permitir teclas de control
    if (allowed.includes(e.key)) return;

    // Permitir solo dígitos
    if (/^[0-9]$/.test(e.key)) return;

    // Permitir punto decimal solo si no hay ya uno
    if (e.key === '.' && !e.target.value.includes('.')) return;

    // Bloquear todo lo demás: +, -, *, letras, etc.
    e.preventDefault();
}


export function actionInputPayment(e) {
    const paymentId = e.target.getAttribute('data-id');
    const raw = e.target.value;

    // Limitar a 2 decimales mientras escribe
    if (raw.includes('.')) {
        const parts = raw.split('.');
        if (parts[1]?.length > 2) {
            e.target.value = parseFloat(raw).toFixed(2);
        }
    }

    const amount = parseFloat(parseFloat(raw).toFixed(2)); // siempre 2 decimales


    if (raw === '' || isNaN(amount)) {
        const idx = lstPays.findIndex(i => i.paymentId == paymentId);
        if (idx !== -1) lstPays.splice(idx, 1);
        calculateAmounts();
        return;
    }

    // ── BLOQUEO para métodos que NO son efectivo ──────────────
    // Calcular cuánto han aportado los OTROS métodos (sin el actual)
    const othersPaid = lstPays
        .filter(i => i.paymentId != paymentId)
        .reduce((sum, i) => sum + Number(i.amount || 0), 0);

    const maxAllowed = infoAmounts.total - othersPaid; // lo que aún falta cubrir

    if (paymentId != CASH_ID && amount > maxAllowed) {
        // Corregir el input al máximo permitido y usar ese valor
        e.target.value = maxAllowed > 0 ? maxAllowed.toFixed(2) : '';
        if (maxAllowed <= 0) {
            const idx = lstPays.findIndex(i => i.paymentId == paymentId);
            if (idx !== -1) lstPays.splice(idx, 1);
            calculateAmounts();
            return;
        }
        // Continuar con el valor corregido
        const idx = lstPays.findIndex(i => i.paymentId == paymentId);
        if (idx === -1) lstPays.push({ paymentId, amount: maxAllowed });
        else lstPays[idx].amount = maxAllowed;
        calculateAmounts();
        return;
    }

    // Insertar o actualizar normalmente
    const idx = lstPays.findIndex(i => i.paymentId == paymentId);
    if (idx === -1) lstPays.push({ paymentId, amount });
    else lstPays[idx].amount = amount;

    calculateAmounts();
    console.log('lstPays', lstPays)
}

// ─── CÁLCULO CENTRAL ─────────────────────────────────────────
function calculateAmounts() {
    const cashAmount = lstPays.find(i => i.paymentId == CASH_ID)?.amount || 0;
    const otherAmount = lstPays
        .filter(i => i.paymentId != CASH_ID)
        .reduce((sum, i) => sum + Number(i.amount || 0), 0);

    const totalPaid = cashAmount + otherAmount;
    const total = infoAmounts.total;

    infoAmounts.paid = totalPaid;
    infoAmounts.pending = Math.max(total - totalPaid, 0);
    infoAmounts.change = cashAmount > 0
        ? Math.max(totalPaid - total, 0)  // vuelto solo si hay efectivo
        : 0;

    renderSummary(infoAmounts);
}

export function actionBtnInvoice(card) {
    desactiveBtnsInvoice();

    const btn = card;
    btn.classList.add('active');

    const invoiceTypeId = btn.dataset.id;
    setInvoiceId(invoiceTypeId);
}

export async function actionFormCharge(e) {
    e.preventDefault();

    toastr.clear();
    const isValid = validatePayments(lstPays, infoAmounts);
    if (!isValid.ok) return;

    const result = await Swal.fire({
        title: '¿Desea generar el comprobante de venta?',
        text: "Confirmar",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'SI',
        cancelButtonText: 'NO',
        reverseButtons: true,
        customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
    });

    if (result.isConfirmed) {

        try {

            clearValidationErrors('msgError');

            Swal.fire({
                title: 'Generando comproabante...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const lsyPaysPrepared = lstPays.filter(i => Number(i.amount) > 0);
            const formData = new FormData(e.target);
            formData.append('lst_pays', JSON.stringify(lsyPaysPrepared));
            formData.append('order_id', app.order.order_id);
            formData.append('lstAlertsSelected',JSON.stringify(lstAlertsSelected));
            if (invoiceId) {
                formData.append('invoice_id', invoiceId);
            }

            const res = await axios.post(route('tenant.mostrador_cajero.mostrador.storeInvoice'), formData);

            if (res.data.success) {
                //window.open(res.data.pdf_url, '_blank');
                toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');

                redirect('tenant.mostrador_cajero.mostrador.index');
            } else {
                toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                Swal.close();
            }

        } catch (error) {
            Swal.close();
            if (error.response && error.response.status === 422) {
                const errors = error.response.data.errors;
                paintValidationErrors(errors, 'mdlcharge_error');
                return;
            }
        }

    } else {

        Swal.fire({
            icon: 'info',
            title: 'Operación cancelada',
            text: 'No se realizaron acciones.',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        });

    }
}

export function setState() {
    infoAmounts.total = parseFloat(amounts.totalPay);
    infoAmounts.pending = parseFloat(amounts.totalPay);
}
