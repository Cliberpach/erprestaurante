import { iniciarDataTableProductos } from "./datatables"
import { events } from "./events";
import { loadSelectGlobal, loadTomSelect } from "./tomselect";

document.addEventListener('DOMContentLoaded', () => {
    iniciarDataTableProductos();
    loadTomSelect();
    loadSelectGlobal('tomselect_pay');
    events();
})
