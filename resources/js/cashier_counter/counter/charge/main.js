import { mainMdlCharge } from "../modals/main";
import { loadDataPreview } from "./action";
import { eventsCCounter } from "./events"
import { loadSelectsCharge } from "./tomselect";

document.addEventListener('DOMContentLoaded', () => {
    loadDataPreview();
    mainMdlCharge();
    loadSelectsCharge();
    eventsCCounter();
    app.eventsAdd();
})


