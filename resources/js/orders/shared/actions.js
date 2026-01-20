import { dtDetail, lastIsDesktop, lstDetail, setDtDetail, setLastIsDesktop } from "./state";

export function actionDesktopQuery(e) {
    if (e.matches !== lastIsDesktop) {
        setLastIsDesktop(e.matches);
        toggleOrderDetailView(lstDetail);
    }
}

export function toggleOrderDetailView(lstDetail) {
    const table = document.getElementById('tbl_order_detail');
    const cards = document.getElementById('cards_dishes');

    if (!table || !cards) return;

    if (isDesktop()) {
        // DESKTOP → TABLA
        table.classList.remove('d-none');
        cards.classList.add('d-none');

        clearTable('tbl_order_detail');
        setDtDetail(destroyDataTable(dtDetail));
        paintTblDetail(lstDetail);
        setDtDetail(loadDataTableSimple('tbl_order_detail'));

    } else {
        // MOBILE / TABLET → CARDS
        table.classList.add('d-none');
        cards.classList.remove('d-none');

        clearTable('tbl_order_detail');
        setDtDetail(destroyDataTable(dtDetail));
        paintCardsDetail(lstDetail);
    }
}
