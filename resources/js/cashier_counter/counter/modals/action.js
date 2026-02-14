import { amounts } from "../charge/state";
import { change, customerSelect, invoiceId, lstPays, paymentMethodSelect, setChange, setInvoiceId } from "./state";
import { desactiveBtnsInvoice, disabledInputsPayment, enabledInputsPayment, paintChange, setDataFormCharge } from "./ui";

export function openMdlCharge() {
    setConfigDefault();
    setDataFormCharge(amounts);
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
    customerSelect.setValue(1);
    const invoiceCard = document.querySelector('#invoice-type-67');
    if (invoiceCard) {
        invoiceCard.dispatchEvent(new MouseEvent('click', {
            bubbles: true,
            cancelable: true,
            view: window
        }));
    }
}

export function actionInputPayment(e) {
    const paymentId = e.target.getAttribute('data-id');
    const amount = parseFloat(e.target.value);

    if (e.target.value === '') {
        const indexPay = lstPays.findIndex(i => i.paymentId == paymentId);
        if (indexPay !== -1) {
            lstPays.splice(indexPay, 1);
        }
        calculateChange();
    }

    if (isNaN(amount)) {
        return;
    }

    let newPay = { paymentId, amount };

    const indexPay = lstPays.findIndex(i => i.paymentId == paymentId);
    if (indexPay === -1) {
        lstPays.push(newPay);
    } else {
        lstPays[indexPay].amount = amount;
    }

    calculateChange();
    paintChange(change);
}

function calculateChange() {
    const totalPay = lstPays.reduce((sum, item) => {
        return sum + Number(item.amount || 0);
    }, 0);

    const totalOrder = amounts.totalPay;
    let _change = totalPay - totalOrder;
    if (_change < 0) _change = 0;
    setChange(_change);
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
    /*
    const isValid = validationStoreQuote();
    if (!isValid) {
        return;
    }
    */

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

            const formData = new FormData(e.target);
            formData.append('lst_pays', JSON.stringify(lstPays));
            formData.append('order_id', app.order.order_id);
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
