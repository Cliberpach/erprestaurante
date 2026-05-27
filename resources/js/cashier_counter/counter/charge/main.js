import { loadMdlPassword } from "../../../components/mdl_password/main";
import { mainMdlCharge } from "../modals/main";
import { loadDataPreview } from "./action";
import { eventsCCounter } from "./events"
import { getAmounts, getPaymentConditionSelect, paymentConditionSelect } from "./state";
import { loadSelectsCharge } from "./tomselect";
import { disabledInputDiscount, enableDiscount } from "./ui";

document.addEventListener('DOMContentLoaded', () => {
    loadDataPreview();
    mainMdlCharge({
        getAmounts: getAmounts,
        getPaymentConditionSelect
    });
    loadMdlPassword({
        title: 'Ingresar password',
        subtitle: 'Desbloquear descuento',
        onSuccess: disabledInputDiscount,
    });
    loadSelectsCharge();
    enableDiscount(app.configDiscount);
    eventsCCounter();
    app.eventsAdd();
})


