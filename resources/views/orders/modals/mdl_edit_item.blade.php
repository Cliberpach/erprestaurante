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
    const itemEdition = {
        id: null
    };

    function eventsMdlEditItem() {
        document.addEventListener('click', (e) => {

            const btnEdit = e.target.closest('.btnEditItem');
            if (btnEdit) {
                toastr.clear();

                const producto_id = btnEdit.getAttribute('data-producto-id');

                if (!producto_id) {
                    toastr.error('ERROR AL SETEAR PRODUCTO');
                    return;
                }

                setProducto(producto_id);
                openMdlEditItem();
            }


        })

        document.querySelector('#formEditItem').addEventListener('submit', (e) => {

            mostrarAnimacion1();
            e.preventDefault();
            const dataFormEdit = getDataFormEdit();
            const validacionFormEdit = validarDataFormEdit(dataFormEdit);

            if (validacionFormEdit) {
                const actualizacion = actualizarItem(dataFormEdit);
                if (actualizacion) {
                    clearTable('tbl_order_detail');
                    dtCompraDetalle = destroyDataTable(dtCompraDetalle);
                    paintTblDetail(lstDetail);
                    iniciarDataTableCompraDetalle();

                    calculateAmounts();
                    paintAmounts();
                    $('#mdlEditItem').modal('hide');
                    toastr.success('ITEM ACTUALIZADO');
                }
            }
            ocultarAnimacion1();

        })
    }

    function openMdlEditItem() {
        $('#mdlEditItem').modal('show');
    }

    function actualizarItem(dataFormEdit) {

        //======= GRABANDO =========
        const indiceItem = lstDetail.findIndex((lcd) => {
            return lcd.id == itemEdition.id;
        })

        if (indiceItem === -1) {
            toastr.error('NO SE ENCONTRÓ EL ITEM A EDITAR');
            return false;
        }

        lstDetail[indiceItem].quantity = dataFormEdit.cantidad;
        lstDetail[indiceItem].total     =   lstDetail[indiceItem].sale_price * parseFloat(dataFormEdit.cantidad);
        return true;
    }

    function getDataFormEdit() {
        const cantidad = document.querySelector('#item_cantidad_edit').value;
        const data = {
            cantidad
        };
        return data;
    }

    function validarDataFormEdit(data) {
        let validacion = false;

        if (data.cantidad === null) {
            toastr.error('DEBE INGRESAR UNA CANTIDAD!!');
            return validacion;
        }

        if (data.cantidad == 0) {
            toastr.error('LA CANTIDAD DEBE SER MAYOR A 0!!');
            return validacion;
        }

        return true;
    }

    function setProducto(itemId) {

        const itemIndice = lstDetail.findIndex((lcd) => {
            return lcd.id == itemId;
        })

        if (itemIndice === -1) {
            toastr.error('NO SE ENCUENTRA EL ITEM EN EL DETALLE!!');
            return;
        }
        0

        const itemFind = lstDetail[itemIndice];

        document.querySelector('#item_nombre_edit').textContent = itemFind.name;
        document.querySelector('#item_tipo_plato_edit').textContent = itemFind.type_name;
        document.querySelector('#item_precio_compra_edit').textContent = formatSoles(itemFind.purchase_price);
        document.querySelector('#item_precio_venta_edit').textContent = formatSoles(itemFind.sale_price);

        document.querySelector('#item_cantidad_edit').value = itemFind.quantity;
        itemEdition.id = itemFind.id;

    }
</script>
