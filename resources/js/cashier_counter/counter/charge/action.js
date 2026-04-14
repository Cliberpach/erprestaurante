import { openMdlPassword } from "../../../components/mdl_password/action";
import { getStatePassword } from "../../../components/mdl_password/state";
import { openMdlCharge } from "../modals/action";
import { amounts, dtDetail, lstDetail, lstDetailCanceled, setDtDetail, setLstDetail, setLstDetailCanceled } from "./state";
import { paintAmounts, paintTblDetail } from "./ui";

export function loadDataPreview() {
    setLstDetail(app.lstDetail);
    setLstDetailCanceled(app.lstDetailCanceled);
    setAmounts(app.order);
    paintTblDetail(lstDetail, lstDetailCanceled);
    paintAmounts(amounts);
    const instanceDt = loadDataTableSimple('dt-detail', {
        columnDefs: [
            { targets: [0], visible: false }
        ]
    });
    setDtDetail(instanceDt);
}

export function actionChargeCreate() {
    openMdlCharge();
}

function setAmounts(order) {
    amounts.subTotal = order.subtotal;
    amounts.tax = order.igv;
    amounts.total = order.total;
    amounts.discount = 0;
    amounts.totalPay = order.total;
    console.log('amounts', amounts);
}

export function actionChangeStatus(status) {
    if (!status || status == 'TODO') {
        dtDetail.column(0).search("").draw();
        return;
    }
    dtDetail.column(0).search(status).draw();
}

export function actionBtnDiscount() {
    if (getStatePassword()) return;
    openMdlPassword();
}

export function actionInputDiscount(e) {
    let value = e.target.value;

    // 1. Limpiar: solo números y punto
    value = value.replace(/[^0-9.]/g, '');

    // 2. Evitar múltiples puntos
    const parts = value.split('.');
    if (parts.length > 2) {
        value = parts[0] + '.' + parts[1];
    }

    // 3. Limitar a 2 decimales
    if (value.includes('.')) {
        const [int, dec] = value.split('.');
        value = int + '.' + dec.slice(0, 2);
    }

    // 4. Convertir a número
    let discount = parseFloat(value);
    if (isNaN(discount)) discount = 0;

    // 5. No puede ser mayor al total
    const max = amounts.total;

    if (discount > max) {
        discount = parseFloat(max);
    }

    // 6. Guardar valor limpio en input
    e.target.value = discount ? discount : '';

    // 7. Actualizar estado
    amounts.discount = discount;

    // 8. Recalcular total a pagar
    amounts.totalPay = amounts.total - discount;

    paintAmounts(amounts);
}
