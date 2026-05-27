import { loadSimpleSelect } from "../../../utils/selects/simple/main";
import { setPaymentConditionSelect, setFilterStatusSelect, getPaymentConditionSelect } from "./state";

export function loadSelectsCharge() {
    const icon = '<i class="fas fa-tag me-2 text-primary"></i>';
    setFilterStatusSelect(loadSimpleSelect('filter_status', icon));

    const iconCondition = '<i class="fas fa-credit-card text-primary me-2"></i>'
    setPaymentConditionSelect(loadSimpleSelect('payment_condition', iconCondition))
    console.log('select condition',getPaymentConditionSelect())
}


