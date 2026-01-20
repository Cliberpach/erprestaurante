import { route } from "ziggy-js";

export const routes = {
    index: route('tenant.mostrador_mesero.mostrador.index'),
    update: (id) => route('tenant.mostrador_mesero.mostrador.update', { id })
}
