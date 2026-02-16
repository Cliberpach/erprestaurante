import { iniciarDataTableProductos } from "./datatables"
import { events } from "./events";
import { loadSelectGlobal, loadTomSelect } from "./tomselect";

document.addEventListener('DOMContentLoaded', () => {
    const html = document.querySelector('html');
    html.setAttribute('data-app-sidebar', 'mini');
    iniciarDataTableProductos();
    loadTomSelect();
    loadSelectGlobal('tomselect_pay');
    events();
})
