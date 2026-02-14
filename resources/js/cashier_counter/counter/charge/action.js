import { openMdlCharge } from "../modals/action";
import { amounts, lstDetail, lstDetailCanceled, setLstDetail, setLstDetailCanceled } from "./state";
import { paintAmounts, paintTblDetail } from "./ui";

export function loadDataPreview() {
    setLstDetail(app.lstDetail);
    setLstDetailCanceled(app.lstDetailCanceled);
    setAmounts(app.order);
    paintTblDetail(lstDetail,lstDetailCanceled);
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
