import { route } from "ziggy-js";
import { routes } from "./routes";

export async function getNotifications(page) {
    try {
        const { data } = await axios.get(route(routes.getAll), {
            params: {
                page: page
            }
        });

        return data;

    } catch (error) {
        toastr.error(error, 'ERROR EN LA PETICIÓN OBTENER CANTIDAD DE NOTIFICACIONES');
        return null;
    }
}
