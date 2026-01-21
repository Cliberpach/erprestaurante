export function loadTomSelect() {

    const initialCustomer = app.customerFormatted;
    window.clientSelect = new TomSelect('#client_id', {
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
            lastCustomerQuery = str;
        },
        load: async (query, callback) => {
            if (query.length < 3) return callback();
            try {
                const url = `{{ route('tenant.utils.searchCustomer') }}?q=${encodeURIComponent(query)}`;
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

    const paymentMethodsSelect = document.getElementById('payment_method');
    if (paymentMethodsSelect && !paymentMethodsSelect.tomselect) {
        window.paymentMethodsSelect = new TomSelect(paymentMethodsSelect, {
            valueField: 'id',
            labelField: 'description',
            searchField: ['description', 'id'],
            create: false,
            sortField: {
                field: 'id',
            },
            plugins: ['clear_button'],

            render: {
                option: (item, escape) => {
                    const icon = getPaymentIcon(item);
                    return `
                    <div class="d-flex align-items-center gap-2">
                        <i class="${icon.class} ${icon.color}"></i>
                        <span>${escape(item.description)}</span>
                    </div>
                `;
                },
                item: (item, escape) => {
                    const icon = getPaymentIcon(item);
                    return `
                    <div class="d-flex align-items-center gap-2">
                        <i class="${icon.class} ${icon.color}"></i>
                        <span>${escape(item.description)}</span>
                    </div>
                `;
                }
            }
        });
    }

    function getPaymentIcon(item) {
        const text = item.description.toLowerCase();

        // 📱 YAPE → morado
        if (text.includes('yape')) {
            return {
                class: 'fas fa-mobile-screen-button',
                color: 'text-purple'
            };
        }

        // 📱 PLIN → celeste
        if (text.includes('plin')) {
            return {
                class: 'fas fa-mobile-screen-button',
                color: 'text-info'
            };
        }

        // 💳 TARJETAS
        if (text.includes('tarjeta') || text.includes('visa') || text.includes('master')) {
            return {
                class: 'fas fa-credit-card',
                color: 'text-primary'
            };
        }

        // 🏦 BANCOS / TRANSFERENCIA
        if (text.includes('banco') || text.includes('transfer')) {
            return {
                class: 'fas fa-building-columns',
                color: 'text-info'
            };
        }

        // 💵 EFECTIVO
        if (text.includes('efectivo')) {
            return {
                class: 'fas fa-money-bill-wave',
                color: 'text-success'
            };
        }

        // 👛 DEFAULT
        return {
            class: 'fas fa-wallet',
            color: 'text-secondary'
        };
    }

}
