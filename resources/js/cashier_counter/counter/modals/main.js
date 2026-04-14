import { loadDtAlerts } from "../../../utils/datatables/alerts/main";
import { setState } from "./action";
import { eventsMdlCharge } from "./events";
import { setConfigMdlCharge } from "./state";
import { loadSelectMdlCharge } from "./tomselect";

export function mainMdlCharge(config) {
    setConfigMdlCharge(config);
    loadSelectMdlCharge();
    setState();
    loadDtAlerts();
    eventsMdlCharge();
}
