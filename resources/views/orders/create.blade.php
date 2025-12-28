@extends('layouts.template')

@section('title')
    Pedidos
@endsection

@section('content')
    @include('orders.modals.mdl_dishes')
    @include('orders.modals.mdl_products')
    @include('orders.modals.mdl_edit_item')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h4 class="card-title mb-md-0 mb-2">REGISTRAR PEDIDO</h4>

            <div class="d-flex flex-wrap gap-2">

            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    @include('orders.forms.form_create')
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-12 d-flex justify-content-end">

                    <!-- BOTÓN VOLVER -->
                    <button type="button" class="btn btn-danger me-1"
                        onclick="redirect('tenant.abastecimiento.programacion.index')">
                        <i class="fas fa-arrow-left"></i> VOLVER
                    </button>

                    <!-- BOTÓN REGISTRAR -->
                    <button class="btn btn-primary" form="form_create" type="submit">
                        <i class="fas fa-save"></i> REGISTRAR
                    </button>

                </div>

            </div>
        </div>
    </div>
@endsection

<style>
    .swal2-container {
        z-index: 9999999;
    }
</style>

@section('js')
    <script>
        let dtCompraDetalle = null;
        const lstDetail = [];
        let itemSelected = {
            id: null,
            name: null,
            type_name: null,
            purchase_price: null,
            sale_price: null,
            type_item: null,
            quantity: null,
            total: null
        };
        const amounts = {
            subTotal: 0,
            tax: 0,
            totalPay: 0
        }

        document.addEventListener('DOMContentLoaded', () => {
            mostrarAnimacion1();
            loadTomSelect();
            dtCompraDetalle = loadDataTableSimple('tbl_order_detail');
            events();
            ocultarAnimacion1();
        })

        function events() {
            eventsMdlDishes();
            eventsMdlProductos();
            eventsMdlEditItem();

            document.querySelector('#form_create').addEventListener('submit', (e) => {
                e.preventDefault();
                store(e.target);
            })

            document.addEventListener('click', (e) => {

                if (e.target.closest('.btnAgregarProducto')) {

                    actionAddItem();

                }

                const btnDelete = e.target.closest('.btnDeleteItem');
                if (btnDelete) {
                    toastr.clear();

                    const itemId = btnDelete.getAttribute('data-producto-id');

                    const res_delete_item = deleteItem(itemId);

                    if (res_delete_item) {
                        clearTable('tbl_order_detail');
                        dtCompraDetalle = destroyDataTable(dtCompraDetalle);
                        paintTblDetail(lstDetail);
                        iniciarDataTableCompraDetalle();
                        calculateAmounts();
                        paintAmounts();
                        toastr.success('ITEM ELIMINADO!!');
                    }
                }


            })
        }

        function loadTomSelect() {

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

        function actionAddItem() {
            toastr.clear();
            const inputCantidad = document.querySelector('#cantidad');
            const validacion = validationAddItem();

            itemSelected.total = itemSelected.sale_price * parseFloat(inputCantidad.value);

            if (validacion) {
                mostrarAnimacion1();
                addItem({
                    ...itemSelected
                }, inputCantidad.value);

                calculateAmounts();
                paintAmounts();

                clearFormAddItem();
                ocultarAnimacion1();
            }
        }

        function validationAddItem() {

            if (!itemSelected.id) {
                toastr.error(`DEBE SELECCIONAR UN PLATO O PRODUCTO PREVIAMENTE`);
                return false;
            }

            const inputCantidad = document.querySelector('#cantidad');
            if (!inputCantidad.value) {
                toastr.error('DEBE INGRESAR UNA CANTIDAD!!');
                return false;
            }
            if (inputCantidad.value == 0) {
                toastr.error('LA CANTIDAD DEBE SER MAYOR A 0!!');
                return false;
            }

            return true;
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
            destroyDataTable(dtCompraDetalle);
            paintTblDetail(lstDetail);
            iniciarDataTableCompraDetalle();
            toastr.info(`${item.type_name} AGREGADO AL DETALLE`);
        }

        function iniciarDataTableCompraDetalle() {
            dtCompraDetalle = new DataTable('#tbl_order_detail', {
                language: {
                    "lengthMenu": "Mostrar _MENU_ registros por página",
                    "zeroRecords": "No se encontraron resultados",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                    "infoFiltered": "(filtrado de _MAX_ registros totales)",
                    "search": "Buscar:",
                    "paginate": {
                        "first": "Primero",
                        "last": "Último",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    },
                    "loadingRecords": "Cargando...",
                    "processing": "Procesando...",
                    "emptyTable": "No hay datos disponibles en la tabla",
                    "aria": {
                        "sortAscending": ": activar para ordenar la columna de manera ascendente",
                        "sortDescending": ": activar para ordenar la columna de manera descendente"
                    }
                }
            });
        }

        function paintTblDetail(lstItems) {
            let filas = ``;
            lstItems.forEach((item) => {
                filas += `<tr>
                            <th>
                                <div class="d-flex justify-content-center gap-1">

                                    <button class="btn btn-info btn-sm btnEditItem" type="button"
                                    data-producto-id="${item.id}">
                                        <i class="fas fa-edit"></i>
                                    </button>


                                    <button class="btn btn-danger btn-sm btnDeleteItem" type="button"
                                    data-producto-id="${item.id}">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                </div>
                            </th>
                            <td>${item.name}</td>
                            <td>${item.type_item}</td>
                            <td>${item.type_name}</td>
                            <td>${formatSoles(item.sale_price)}</td>
                            <td>${item.quantity}</td>
                            <td>${formatSoles(item.total)}</td>
                            <td>${formatSoles(item.purchase_price)}</td>
                        </tr>`;
            })

            const tbody = document.querySelector('#tbl_order_detail tbody');
            tbody.innerHTML = filas;
        }

        async function store(formCreate) {

            toastr.clear();
            if (lstDetail.length === 0) {
                toastr.error('DEBE AGREGAR AL MENOS UN PRODUCTO EN EL DETALLE!!');
                return;
            }

            const result = await Swal.fire({
                title: '¿Desea registrar la programación?',
                text: "Confirme para continuar",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'SI, registrar',
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
                        title: 'Registrando programación...',
                        text: 'Por favor espere',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const formData = new FormData(formCreate);
                    formData.append('lst_detail', JSON.stringify(lstDetail));

                    const res = await axios.post(route('tenant.abastecimiento.programacion.store'), formData);
                    if (res.data.success) {
                        toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                        redirect('tenant.abastecimiento.programacion.index');
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

        function clearFormAddItem() {

            const inputProducto = document.querySelector('#producto');
            const inputPurchasePrice = document.querySelector('#purchase_price');
            const inputSalePrice = document.querySelector('#sale_price');
            const inputCantidad = document.querySelector('#cantidad');
            const inputStock = document.querySelector('#item_stock');

            inputProducto.value = '';
            inputPurchasePrice.value = '';
            inputSalePrice.value = '';
            inputCantidad.value = '';
            inputStock.value = '';

            itemSelected.id = null;
            itemSelected.name = null;
            itemSelected.type_name = null;
            itemSelected.quantity = null;
            itemSelected.type_item = null;
            itemSelected.total = null;

            clearDishSelected();
            clearProductSelected();
        }

        function calculateAmounts() {
            let igv = @json($igv);
            let totalPay = 0;
            let tax = 0;
            let subTotal = 0;

            lstDetail.forEach((item) => {
                totalPay += parseFloat(item.total);
            });

            subTotal = totalPay / ((100 + igv) / 100);
            tax = totalPay - subTotal;

            amounts.subTotal = subTotal;
            amounts.tax = tax;
            amounts.totalPay = totalPay;
        }

        function paintAmounts() {
            document.querySelector('#subtotal_amount').innerText = formatSoles(amounts.subTotal);
            document.querySelector('#igv_amount').innerText = formatSoles(amounts.tax);
            document.querySelector('#total_amount').innerText = formatSoles(amounts.totalPay);
        }
    </script>
@endsection
