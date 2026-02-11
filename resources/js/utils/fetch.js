import { routesUtil } from "./routes";

export async function validateProductStock({ warehouseId, productId, quantity, orderId = null }) {
    try {
        const res = await axios.get(routesUtil.validateProductStock({
            warehouseId,
            productId,
            quantity,
            orderId
        }));

        if (res.data.success) {
            //toastr.info(res.data.message, 'OPERACIÓN COMPLETADA');
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

export async function validateDishStock({ programmingId, dishId, quantity, orderId = null }) {
    try {
        const res = await axios.get(routesUtil.validateDishStock({
            programmingId,
            dishId,
            quantity,
            orderId
        }));

        if (res.data.success) {
            //toastr.info(res.data.message, 'OPERACIÓN COMPLETADA');
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

export async function getBankAccountPayment(paymentMethodId) {
    try {
        const res = await axios.get(routesUtil.getBankAccountPayment(paymentMethodId));
        if (res.data.success) {
            toastr.info(res.data.message, 'OPERACIÓN COMPLETADA');
            return res;
        } else {
            toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
            return null;
        }
    } catch (error) {
        toastr.error(error, 'ERROR EN LA PETICIÓN OBTENER CUENTA BANCARIA ACTIVA');
        return null;
    }
}

window.validateProductStock = validateProductStock;
window.validateDishStock = validateDishStock;
