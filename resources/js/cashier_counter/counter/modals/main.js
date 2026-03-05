import { loadDtAlerts } from "../../../utils/datatables/alerts/main";
import { setState } from "./action";
import { eventsMdlCharge } from "./events";
import { loadSelectMdlCharge } from "./tomselect";

export function mainMdlCharge() {
    loadSelectMdlCharge();
    setState();
    loadDtAlerts();
    eventsMdlCharge();
}
