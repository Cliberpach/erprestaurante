import { eventsMdlCharge } from "./events";
import { loadSelectMdlCharge } from "./tomselect";

export function mainMdlCharge() {
    loadSelectMdlCharge();
    eventsMdlCharge();
}
