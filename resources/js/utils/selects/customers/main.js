import { setCustomerSelect } from "./state";

export function loadCustomerSelect(initialCustomer,id) {
    //const initialCustomer = app.customerFormatted;
    const instance = new TomSelect(`#${id}`, {
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
        load: async (query, callback) => {
            if (query.length < 3) return callback();
            try {
                const url = route('tenant.utils.searchCustomer', { q: query });
                const response = await fetch(url);
                if (!response.ok) throw new Error('Error al buscar clientes');
                const data = await response.json();
                const results = data.data ?? [];
                callback(results);
                if (results.length === 0) {
                    customerParams.documentSearchCustomer = query;
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

    setCustomerSelect(instance);
}
