import { loadCustomerSelect } from "../../../utils/selects/customers/main";
import { setPaymentMethodSelect } from "./state";

export function loadSelectMdlCharge() {
    loadPaymentMethod();
    loadCustomerSelect(app.customerFormatted, 'customer_id_mdlcharge');
}

export function loadPaymentMethod() {
    const paymentMethodSelect = document.getElementById('payment_method_mdlcharge');
    if (paymentMethodSelect && !paymentMethodSelect.tomselect) {
        const instance = new TomSelect(paymentMethodSelect, {
            valueField: 'id',
            labelField: 'description',
            searchField: ['description', 'id'],
            create: false,
            plugins: ['clear_button'],
            render: {
                option: (item, escape) => `
                            <div>
                                ${escape(item.description)}
                            </div>
                        `,
                item: (item, escape) => `
                            <div>${escape(item.description)}</div>
                        `
            }
        });
        setPaymentMethodSelect(instance);
    }
}
