import { setConfigDefault } from "./action";
import { eventsMdlCharge } from "./events";
import { loadSelectMdlCharge } from "./tomselect";

export function mainMdlCharge() {
    loadSelectMdlCharge();
    eventsMdlCharge();
    setConfigDefault();
}
