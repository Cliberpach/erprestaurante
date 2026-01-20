import { validateDishStock, validateProductStock } from "../../utils/fetch";
import { routes } from "./routes";
import { amounts, dtDetail, elementsUI, itemSelected, lstDetail, setDtDetail, setLstDetail } from "./state";
import { clearFormAddItem, paintAmounts, paintTblDetail } from "./ui";

export async function actionFormStore(formUpdate) {

    toastr.clear();
    if (lstDetail.length === 0) {
        toastr.error('DEBE AGREGAR AL MENOS UN PRODUCTO EN EL DETALLE!!');
        return;
    }

    const result = await Swal.fire({
        title: '¿Desea registrar el pedido?',
        text: "Confirme para continuar",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'SI',
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
                title: 'Registrando pedido...',
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

            const res = await axios.post(routes.store, formData);

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
    itemSelected.total = itemSelected.sale_price * parseFloat(inputCantidad.value);

    const validacion = await validationAddItem();

    if (validacion) {
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

    const indiceItem = lstDetail.findIndex((i) => {
        return i.id == item.id && i.type_item === i.type_item;
    })

    if (indiceItem !== -1) {
        toastr.error(`EL ${item.type_name} YA EXISTE EN EL DETALLE`);
        return;
    }

    lstDetail.push(item);
    clearTable('tbl_order_detail');
    setDtDetail(destroyDataTable(dtDetail));
    paintTblDetail(lstDetail);
    setDtDetail(loadDataTableSimple('tbl_order_detail'));
    toastr.info(`${item.type_name} AGREGADO AL DETALLE`);
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

    const itemId = btn.getAttribute('data-producto-id');

    const res = deleteItem(itemId);
    if (res) {
        clearTable('tbl_order_detail');
        setDtDetail(destroyDataTable(dtDetail));
        paintTblDetail(lstDetail);
        setDtDetail(loadDataTableSimple('tbl_order_detail'));
        calculateAmounts(amounts);
        paintAmounts(amounts);
        toastr.success('ITEM ELIMINADO!!');
    }
}


function deleteItem(itemId) {

    const indiceItem = lstDetail.findIndex((lcd) => {
        return lcd.id == itemId;
    })

    if (indiceItem === -1) {
        toastr.error('NO SE ENCONTRÓ EL ITEM EN EL DETALLE!!!');
        return false;
    }

    lstDetail.splice(indiceItem, 1);
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
            quantity: itemSelected.quantity
        }

        const res = await validateProductStock(params);
        if (!res || !res.data.success) return;
    }

    if (itemSelected.type_item === 'PLATO') {
        const params = {
            programmingId: itemSelected.programming_id,
            dishId: itemSelected.id,
            quantity: itemSelected.quantity
        }

        const res = await validateDishStock(params);
        if (!res || !res.data.success) return;
    }

    return true;
}

window.calculateAmounts = calculateAmounts;
window.paintAmounts = paintAmounts;
