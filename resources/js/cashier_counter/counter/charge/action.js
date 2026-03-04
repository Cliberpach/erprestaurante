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
