import { loadSimpleSelect } from "../../../utils/selects/simple/main";
import { setFilterStatusSelect } from "./state";

export function loadSelectsCharge() {
    const icon = '<i class="fas fa-tag me-2 text-primary"></i>';
    setFilterStatusSelect(loadSimpleSelect('filter_status', icon));
}
