import { routesUtil } from "../../utils/routes";
import { actionChangeMethodPay } from "./action";
import { app, lastCustomerQuery, setLastCustomerQuery } from "./states";

export function loadTomSelect() {

    const initialCustomer = app.customerFormatted;
    window.clientSelect = new TomSelect('#customer_id', {
        valueField: 'id',
        options: [initialCustomer],
        items: [initialCustomer.id],
        labelField: 'full_name',
        searchField: ['full_name'],
        plugins: ['clear_button'],
        placeholder: 'Seleccione un cliente',
        maxOptions: 20,
        create: false,
        preload: false,
        onType: (str) => {
            setLastCustomerQuery(str);
        },
        load: async (query, callback) => {
            if (query.length < 3) return callback();
            try {
                const url = routesUtil.searchCustomer(query, null);
                const response = await fetch(url);
                if (!response.ok) throw new Error('Error al buscar clientes');
                const data = await response.json();
                const results = data.data ?? [];
                callback(results);
                if (results.length === 0) {
                    customerParams.documentSearchCustomer = lastCustomerQuery;
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
            item: (item, escape) => `<div>${escape(item.full_name)}</div>`,
            no_results: function (data, escape) {
                return `
                            <div class="no-results">
                                <i class="fas fa-search" style="margin-right:6px; color:#17a2b8;"></i>
                                Sin resultados
                            </div>
                        `;
            }
        }
    });
}

export function loadNewSelect(id) {
    const select = document.getElementById(id);
    if (select && !select.tomselect) {
        const instance = new TomSelect(select, {
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
    }
}

export function loadSelectGlobal(className) {
    document.querySelectorAll(`.${className}`).forEach(el => {
        if (el.tomselect) return;

        new TomSelect(el, {
            dropdownParent: 'body',
            placeholder: el.dataset.placeholder || 'Seleccionar',
            create: el.dataset.create === 'true',
            plugins: el.dataset.clear === 'true' ? ['clear_button'] : [],
            onChange(value) {
                const index = el.dataset.index;
                console.log('index',index);
                actionChangeMethodPay(value, index);
            }
        });
    });
}


export function destroySelectGlobal(className) {
    document.querySelectorAll(`.${className}`).forEach(el => {
        if (el.tomselect) {
            el.tomselect.destroy();
        }
    });
}
