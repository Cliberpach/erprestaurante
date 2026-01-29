import { mainMdlCharge } from "../modals/main";
import { loadDataPreview } from "./action";
import { eventsCCounter } from "./events"

document.addEventListener('DOMContentLoaded', () => {
    mainMdlCharge();
    loadDataPreview();
    eventsCCounter();
    app.eventsAdd();
})


