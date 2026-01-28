import { openMdlCharge } from "../modals/action";
import { amounts, lstDetail, setLstDetail } from "./state";
import { paintAmounts, paintTblDetail } from "./ui";

export function loadDataPreview() {
    setLstDetail(app.lstDetail);
    setAmounts(app.order);
    paintTblDetail(lstDetail);
    paintAmounts(amounts);
}

export function actionChargeCreate() {
    openMdlCharge();
}

function setAmounts(order) {
    amounts.subTotal = order.subtotal;
    amounts.tax = order.igv;
    amounts.totalPay = order.total;
}
