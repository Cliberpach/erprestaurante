import { route } from "ziggy-js";

export const routesUtil = {
    validateProductStock: ({ warehouseId, productId, quantity,orderId = null}) =>
        route('tenant.utils.validatedProductStock', {
            warehouse_id: warehouseId,
            product_id: productId,
            quantity,
            order_id:orderId
        }),
    validateDishStock: ({ programmingId, dishId, quantity,orderId = null}) =>
        route('tenant.utils.validatedDishStock', {
            programming_id: programmingId,
            dish_id: dishId,
            quantity,
            order_id:orderId
        })
};
