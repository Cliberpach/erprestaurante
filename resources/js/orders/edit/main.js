import { loadDataEdit } from "./action";
import { events } from "./events"
import { setDtDetail } from "./state";
import { loadTomSelect } from "./tomselect";

document.addEventListener('DOMContentLoaded', () => {
    mostrarAnimacion1();
    loadTomSelect();
    setDtDetail(loadDataTableSimple('tbl_order_detail'));
    loadDataEdit();
    events();
    ocultarAnimacion1();
})
