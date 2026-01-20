import { route } from "ziggy-js";

export const routesUtil = {
    validateProductStock: ({ warehouseId, productId, quantity}) =>
        route('tenant.utils.validatedProductStock', {
            warehouse_id: warehouseId,
            product_id: productId,
            quantity
        }),
    validateDishStock: ({ programmingId, dishId, quantity}) =>
        route('tenant.utils.validatedDishStock', {
            programming_id: programmingId,
            dish_id: dishId,
            quantity
        })
};
