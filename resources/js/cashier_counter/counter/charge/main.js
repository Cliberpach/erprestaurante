import { mainMdlCharge } from "../modals/main";
import { loadDataPreview } from "./action";
import { eventsCCounter } from "./events"
import { loadSelectsCharge } from "./tomselect";

document.addEventListener('DOMContentLoaded', () => {
    mainMdlCharge();
    loadDataPreview();
    loadSelectsCharge();
    eventsCCounter();
    app.eventsAdd();
})


