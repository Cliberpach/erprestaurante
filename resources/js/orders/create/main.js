import { isDesktop } from "../../utils/utils";
import { setDtDetail } from "../shared/state";
import { events } from "./events"
import { loadFilePond } from "./filepond";
import { loadTomSelect } from "./tomselect";

document.addEventListener('DOMContentLoaded', () => {
    mostrarAnimacion1();
    loadTomSelect();
    loadFilePond();
    if (isDesktop()) {
        setDtDetail(loadDataTableSimple('tbl_order_detail'));
    }
    events();
    ocultarAnimacion1();
})
