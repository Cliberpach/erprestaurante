import { actionBtnInvoice, actionFormCharge, actionInputPayment, actionPaymentMethod, onKeyDownPayment } from "./action";
import { paymentMethodSelect } from "./state";

export function eventsMdlCharge() {
    paymentMethodSelect.on('change', function (value) {
        actionPaymentMethod(value);
    });
    eventsInputMdlCharge();
    eventsClickMdlCharge();
    eventsSubmitMdlCharge();
    eventsKeyDown();
}

function eventsInputMdlCharge() {
    document.addEventListener('input', (e) => {
        if (e.target.classList.contains('input-payment')) {
            actionInputPayment(e);
        }
    })
}

function eventsKeyDown() {
    document.querySelector('.col-payments').addEventListener('keydown', (e) => {
        if (e.target.classList.contains('input-payment')) {
            onKeyDownPayment(e);
        }
    })
}

function eventsClickMdlCharge() {
    document.querySelectorAll('.comprobante-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const card = e.target.closest('.comprobante-btn');
            if (!card) return;
            actionBtnInvoice(card);
        });
    });
}

function eventsSubmitMdlCharge() {
    document.querySelector('#form-charge').addEventListener('submit', (e) => {
        actionFormCharge(e);
    })
}
