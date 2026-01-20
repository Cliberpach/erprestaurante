import { routesUtil } from "./routes";

export async function validateProductStock({ warehouseId, productId, quantity }) {
    try {
        const res = await axios.get(routesUtil.validateProductStock({
            warehouseId,
            productId,
            quantity
        }));

        if (res.data.success) {
            toastr.info(res.data.message, 'OPERACIÓN COMPLETADA');
            return res;
        } else {
            toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
            return null;
        }
    } catch (error) {
        toastr.error(error, 'ERROR EN LA PETICIÓN VALIDAR STOCK PRODUCTO');
        return null;
    }
}

export async function validateDishStock({ programmingId, dishId, quantity }) {
    try {
        const res = await axios.get(routesUtil.validateDishStock({
            programmingId,
            dishId,
            quantity
        }));

        if (res.data.success) {
            toastr.info(res.data.message, 'OPERACIÓN COMPLETADA');
            return res;
        } else {
            toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
            return null;
        }
    } catch (error) {
        toastr.error(error, 'ERROR EN LA PETICIÓN VALIDAR STOCK PLATO');
        return null;
    }
}

window.validateProductStock = validateProductStock;
window.validateDishStock = validateDishStock;
