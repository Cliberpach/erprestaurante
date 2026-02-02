import { validateDishStock, validateProductStock } from "../../utils/fetch";
import { isDesktop } from "../../utils/utils";
import { dtDetail, elementsUI, lstDetail, setDtDetail, setLstDetail } from "../shared/state";
import { clearFormAddItem, paintCardsDetail } from "../shared/ui";
import { routes } from "./routes";
import { amounts, itemSelected } from "./state";
import { paintAmounts, paintTblDetail } from "./ui";

export async function actionFormUpdate(formUpdate) {

    toastr.clear();
    if (lstDetail.length === 0) {
        toastr.error('DEBE AGREGAR AL MENOS UN PRODUCTO EN EL DETALLE!!');
        return;
    }

    const result = await Swal.fire({
        title: '¿Desea actualizar el pedido?',
        text: "Confirme para continuar",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'SI, actualizar',
        cancelButtonText: 'NO',
        reverseButtons: true,
        customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
    });

    if (result.isConfirmed) {

        try {

            clearValidationErrors('msgError');

            Swal.fire({
                title: 'Actualizando pedido...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData(formUpdate);
            formData.append('lst_detail', JSON.stringify(lstDetail));
            formData.append('table_id', app.tableId);
            formData.append('_method', 'PUT');

            const res = await axios.post(routes.update(app.orderId), formData);

            if (res.data.success) {
                toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                window.location.href = routes.index;

            } else {
                toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                Swal.close();
            }

        } catch (error) {
            Swal.close();
            if (error.response && error.response.status === 422) {
                const errors = error.response.data.errors;
                paintValidationErrors(errors, 'error');
                return;
            }
        }

    } else {

        Swal.fire({
            icon: 'info',
            title: 'Operación cancelada',
            text: 'No se realizaron acciones.',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        });

    }
}

export async function actionAddItem() {
    toastr.clear();
    mostrarAnimacion1();

    const inputCantidad = elementsUI.inputQuantity;
    itemSelected.quantity = inputCantidad.value;

    const validacion = await validationAddItem();

    itemSelected.total = itemSelected.sale_price * parseFloat(inputCantidad.value);
    itemSelected.observation = elementsUI.inputObservation.value.trim();


    if (validacion) {
        mostrarAnimacion1();
        addItem({
            ...itemSelected
        }, inputCantidad.value);

        calculateAmounts(amounts);
        paintAmounts(amounts);

        clearFormAddItem(itemSelected);
    }
    ocultarAnimacion1();
}

function addItem(item, cantidad) {
    item.quantity = cantidad;

    lstDetail.push(item);

    if (isDesktop()) {
        clearTable('tbl_order_detail');
        setDtDetail(destroyDataTable(dtDetail));
        paintTblDetail(lstDetail);
        setDtDetail(loadDataTableSimple('tbl_order_detail'));
    } else {
        paintCardsDetail(lstDetail);
    }

    toastr.info(`${item.name} AGREGADO AL DETALLE`);
}


function calculateAmounts(_amounts) {
    let igv = app.companyIgv;
    let totalPay = 0;
    let tax = 0;
    let subTotal = 0;
    console.log(lstDetail)
    lstDetail.forEach((item) => {
        totalPay += parseFloat(item.total);
    });

    subTotal = totalPay / ((100 + igv) / 100);
    tax = totalPay - subTotal;

    _amounts.subTotal = subTotal;
    _amounts.tax = tax;
    _amounts.totalPay = totalPay;
}

export function actionDeleteItem(btn) {
    toastr.clear();

    const index = btn.getAttribute('data-index');

    const res = deleteItem(index);
    if (res) {

        if (isDesktop()) {
            clearTable('tbl_order_detail');
            setDtDetail(destroyDataTable(dtDetail));
            paintTblDetail(lstDetail);
            setDtDetail(loadDataTableSimple('tbl_order_detail'));
        } else {
            paintCardsDetail(lstDetail);
        }

        calculateAmounts(amounts);
        paintAmounts(amounts);
        toastr.success('ITEM ELIMINADO!!');
    }
}

function deleteItem(index) {
    if (index === -1) {
        toastr.error('NO SE ENCONTRÓ EL ITEM EN EL DETALLE!!!');
        return false;
    }

    lstDetail.splice(index, 1);
    return true;
}

async function validationAddItem() {

    if (!itemSelected.id) {
        toastr.error(`DEBE SELECCIONAR UN PLATO O PRODUCTO PREVIAMENTE`);
        return false;
    }

    const inputCantidad = elementsUI.inputQuantity;
    if (!inputCantidad.value) {
        toastr.error('DEBE INGRESAR UNA CANTIDAD!!');
        return false;
    }
    if (inputCantidad.value == 0) {
        toastr.error('LA CANTIDAD DEBE SER MAYOR A 0!!');
        return false;
    }

    if (itemSelected.type_item === 'PRODUCTO') {
        const params = {
            warehouseId: itemSelected.warehouse_id,
            productId: itemSelected.id,
            quantity: itemSelected.quantity,
            orderId: app.orderId
        }
        console.log('params', params)
        const res = await validateProductStock(params);
        if (!res || !res.data.success) return;
    }

    if (itemSelected.type_item === 'PLATO') {
        const params = {
            programmingId: itemSelected.programming_id,
            dishId: itemSelected.id,
            quantity: itemSelected.quantity,
            orderId: app.orderId
        }

        const res = await validateDishStock(params);
        if (!res || !res.data.success) return;
    }

    return true;
}

export function loadDataEdit() {
    setLstDetail(app.lstDetail);

    //====== PRODUCTS =======
    if (isDesktop()) {
        setDtDetail(destroyDataTable(dtDetail));
        clearTable('tbl_order_detail');
        paintTblDetail(lstDetail);
        setDtDetail(loadDataTableSimple('tbl_order_detail'));
    } else {
        paintCardsDetail(lstDetail);
    }

    calculateAmounts(amounts);
    paintAmounts(amounts);
}

window.calculateAmounts = calculateAmounts;
window.paintAmounts = paintAmounts;
