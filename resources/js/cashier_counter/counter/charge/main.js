import { loadMdlPassword } from "../../../components/mdl_password/main";
import { mainMdlCharge } from "../modals/main";
import { loadDataPreview } from "./action";
import { eventsCCounter } from "./events"
import { getAmounts } from "./state";
import { loadSelectsCharge } from "./tomselect";
import { disabledInputDiscount } from "./ui";

document.addEventListener('DOMContentLoaded', () => {
    loadDataPreview();
    mainMdlCharge({
        getAmounts:getAmounts
    });
    loadMdlPassword({
        title: 'Ingresar password',
        subtitle: 'Desbloquear descuento',
        onSuccess: disabledInputDiscount,
    });
    loadSelectsCharge();
    eventsCCounter();
    app.eventsAdd();
})


