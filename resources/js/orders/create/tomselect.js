import { loadCustomerSelect } from "../../utils/selects/customers/main";

export function loadTomSelect() {

    loadCustomerSelect(app.customerFormatted);

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

