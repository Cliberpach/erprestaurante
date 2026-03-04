import { setState } from "./action";
import { eventsMdlCharge } from "./events";
import { loadSelectMdlCharge } from "./tomselect";

export function mainMdlCharge() {
    loadSelectMdlCharge();
    setState();
    eventsMdlCharge();
}
