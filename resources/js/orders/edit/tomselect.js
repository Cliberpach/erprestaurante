import { loadCustomerSelect } from "../../utils/selects/customers/main";

export function loadTomSelect() {
    loadCustomerSelect(app.customerFormatted,'client_id');
}

