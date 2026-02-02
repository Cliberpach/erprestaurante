<div class="modal fade" id="mdlEditItem" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Editar Item</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                @include('orders.forms.form_edit_item')

            </div>
            <div class="modal-footer">

                <div class="col-12">
                    <div style="display:flex;justify-content:end;">
                        <button type="button" style="margin-right:5px;" class="btn btn-secondary mr-1"
                            data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" form="formEditItem" class="btn btn-primary">Guardar</button>
                    </div>
                    <span style="color:rgb(219, 155, 35);font-size:14px;font-weight:bold;display:block;">Los campos con
                        * son obligatorios</span>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    const paramsMdlItem = {
        index: null
    };

    function eventsMdlEditItem() {
        document.addEventListener('click', (e) => {

            const btnEdit = e.target.closest('.btnEditItem');
            if (btnEdit) {
                toastr.clear();

                const productIndex = btnEdit.getAttribute('data-index');

                if (!productIndex) {
                    toastr.error('ERROR AL SETEAR PRODUCTO');
                    return;
                }

                setProducto(productIndex);
                openMdlEditItem();
            }


        })

        document.querySelector('#formEditItem').addEventListener('submit', async (e) => {
            actionFormEditItem(e);
        })
    }

    function openMdlEditItem() {
        $('#mdlEditItem').modal('show');
    }

    function updateItem(dataFormEdit, lstDetail) {

        //======= GRABANDO =========
        const indiceItem = paramsMdlItem.index;

        if (indiceItem === -1) {
            toastr.error('NO SE ENCONTRÓ EL ITEM A EDITAR');
            return false;
        }

        lstDetail[indiceItem].quantity = dataFormEdit.cantidad;
        lstDetail[indiceItem].total = lstDetail[indiceItem].sale_price * parseFloat(dataFormEdit.cantidad);
        return true;
    }

    function getDataFormEdit() {
        const cantidad = document.querySelector('#item_cantidad_edit').value;
        const data = {
            cantidad
        };
        return data;
    }

    async function validationFormEdit(data, itemUpdate) {
        let validacion = false;

        if (data.cantidad === null) {
            toastr.error('DEBE INGRESAR UNA CANTIDAD!!');
            return validacion;
        }

        if (data.cantidad == 0) {
            toastr.error('LA CANTIDAD DEBE SER MAYOR A 0!!');
            return validacion;
        }

        if (itemUpdate.type_item === 'PRODUCTO') {
            const params = {
                warehouseId: itemUpdate.warehouse_id,
                productId: itemUpdate.id,
                quantity: data.cantidad
            }

            if ('orderId' in app) {
                params.orderId = app.orderId;
            }

            const res = await validateProductStock(params);
            if (!res || !res.data.success) return;
        }

        if (itemUpdate.type_item === 'PLATO') {
            const params = {
                programmingId: itemUpdate.programming_id,
                dishId: itemUpdate.id,
                quantity: data.cantidad
            }

            if ('orderId' in app) {
                params.orderId = app.orderId;
            }

            const res = await validateDishStock(params);
            if (!res || !res.data.success) return;
        }

        return true;
    }

    function setProducto(itemIndex) {
        const lstDetail = getLstDetail();

        if (itemIndex === -1) {
            toastr.error('NO SE ENCUENTRA EL ITEM EN EL DETALLE!!');
            return;
        }

        const itemFind = lstDetail[itemIndex];

        document.querySelector('#item_nombre_edit').textContent = itemFind.name;
        document.querySelector('#item_tipo_plato_edit').textContent = itemFind.type_name;
        document.querySelector('#item_precio_compra_edit').textContent = formatSoles(itemFind.purchase_price);
        document.querySelector('#item_precio_venta_edit').textContent = formatSoles(itemFind.sale_price);

        document.querySelector('#item_cantidad_edit').value = itemFind.quantity;
        paramsMdlItem.index =  itemIndex;
    }

    async function actionFormEditItem(e) {
        e.preventDefault();
        mostrarAnimacion1();

        const lstDetail = getLstDetail();
        const itemUpdate = lstDetail[paramsMdlItem.index];

        const dataFormEdit = getDataFormEdit();
        const validationForm = await validationFormEdit(dataFormEdit, itemUpdate);

        if (validationForm) {
            const validation = updateItem(dataFormEdit, lstDetail);
            if (validation) {

                if (isDesktop()) {
                    const dtDetail = getDtDetail();
                    clearTable('tbl_order_detail');
                    setDtDetail(destroyDataTable(dtDetail));
                    paintTblDetail(lstDetail);
                    setDtDetail(loadDataTableSimple('tbl_order_detail'));
                } else {
                    paintCardsDetail(lstDetail);
                }

                calculateAmounts(getAmounts());
                paintAmounts(getAmounts());
                $('#mdlEditItem').modal('hide');
                toastr.success('ITEM ACTUALIZADO');
            }
        }
        ocultarAnimacion1();
    }
</script>
