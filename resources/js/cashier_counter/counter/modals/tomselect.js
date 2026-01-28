import { route } from "ziggy-js";
import { setCustomerSelect, setPaymentMethodSelect } from "./state";

export function loadSelectMdlCharge() {
    loadPaymentMethod();
    loadCustomer();
}

export function loadPaymentMethod() {
    const paymentMethodSelect = document.getElementById('payment_method_mdlcharge');
    if (paymentMethodSelect && !paymentMethodSelect.tomselect) {
        const instance = new TomSelect(paymentMethodSelect, {
            valueField: 'id',
            labelField: 'description',
            searchField: ['description', 'id'],
            create: false,
            sortField: {
                field: 'id',
                direction: 'desc'
            },
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

export function loadCustomer() {
    const initialCustomer = app.customerFormatted;
    const instance = new TomSelect('#customer_id_mdlcharge', {
        valueField: 'id',
        labelField: 'full_name',
        options: [initialCustomer],
        items: [initialCustomer.id],
        searchField: ['full_name'],
        plugins: ['clear_button'],
        placeholder: 'Seleccione un cliente',
        maxOptions: 20,
        create: false,
        preload: false,
        // onType: (str) => {
        //     lastCustomerQuery = str;
        // },
        load: async (query, callback) => {
            if (!query.length) return callback();
            try {
                const url = route('tenant.utils.searchCustomer', { q: query });
                const response = await fetch(url);
                if (!response.ok) throw new Error('Error al buscar clientes');
                const data = await response.json();
                const results = data.data ?? [];
                callback(results);
                if (results.length === 0) {
                    //customerParams.documentSearchCustomer = lastCustomerQuery;
                    console.log("No se encontró en BD. Guardado:", window.typedCustomer);
                }
            } catch (error) {
                console.error('Error cargando clientes:', error);
                callback();
            }
        },
        render: {
            option: (item, escape) => `
                <div>
                    <strong>${escape(item.full_name)}</strong><br>
                    <small>${escape(item.email ?? '')}</small>
                </div>
            `,
            item: (item, escape) => `<div>${escape(item.full_name)}</div>`
        }
    });

    setCustomerSelect(instance);
}
