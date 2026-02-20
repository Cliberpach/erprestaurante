@extends('layouts.template')

@section('title')
    Programación
@endsection

@section('content')
    @include('supply.programming.modals.mdl_products')
    @include('supply.programming.modals.mdl_edit_item')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h4 class="card-title mb-md-0 mb-2">EDITAR PROGRAMACIÓN</h4>

            <div class="d-flex flex-wrap gap-2">

            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    @include('supply.programming.forms.form_edit')
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

                    <!-- BOTÓN ACTUALIZAR -->
                    <button class="btn btn-primary" form="form_edit" type="submit">
                        <i class="fas fa-save"></i> ACTUALIZAR
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
        let lstNoteIncome = [];

        document.addEventListener('DOMContentLoaded', () => {
            mostrarAnimacion1();
            loadDataPreview();
            events();
            ocultarAnimacion1();
        })

        function events() {
            eventsMdlProductos();
            eventsMdlEditItem();

            document.querySelector('#form_edit').addEventListener('submit', (e) => {
                e.preventDefault();
                update(e.target);
            })

            document.addEventListener('click', (e) => {

                if (e.target.closest('.btnAgregarProducto')) {

                    toastr.clear();
                    const inputCantidad = document.querySelector('#cantidad');
                    const validacion = validationAddProduct();

                    if (validacion) {
                        mostrarAnimacion1();
                        addProduct({
                            ...product_selected
                        }, inputCantidad.value);
                        clearFormSelectProduct();
                        ocultarAnimacion1();
                    }

                }

                const btnDelete = e.target.closest('.btnDeleteItem');
                if (btnDelete) {
                    toastr.clear();

                    const producto_id = btnDelete.getAttribute('data-producto-id');

                    const res_delete_item = deleteItem(producto_id);

                    if (res_delete_item) {
                        clearTable('tbl_note_income_detail');
                        dtCompraDetalle = destroyDataTable(dtCompraDetalle);
                        pintarTableCompraDetalle(lstNoteIncome);
                        iniciarDataTableCompraDetalle();
                        toastr.success('ITEM ELIMINADO!!');
                    }
                }


            })
        }

        function deleteItem(producto_id) {

            const indiceProducto = lstNoteIncome.findIndex((lcd) => {
                return lcd.product_id == producto_id;
            })

            if (indiceProducto === -1) {
                toastr.error('NO SE ENCONTRÓ EL ITEM EN EL DETALLE!!!');
                return false;
            }

            lstNoteIncome.splice(indiceProducto, 1);
            return true;

        }

        function validationAddProduct() {

            if (!product_selected.product_id) {
                toastr.error('DEBE SELECCIONAR UN PRODUCTO!!');
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

        function addProduct(producto, cantidad) {
            producto.quantity = cantidad;

            const indiceProducto = lstNoteIncome.findIndex((p) => {
                return p.product_id == producto.product_id;
            })

            if (indiceProducto !== -1) {
                toastr.error('EL PRODUCTO YA EXISTE EN EL DETALLE');
                return;
            }

            lstNoteIncome.push(producto);
            clearTable('tbl_note_income_detail');
            destroyDataTable(dtCompraDetalle);
            pintarTableCompraDetalle(lstNoteIncome);
            iniciarDataTableCompraDetalle();
            toastr.info('PRODUCTO AGREGADO AL DETALLE');
        }

        function iniciarDataTableCompraDetalle() {
            dtCompraDetalle = new DataTable('#tbl_note_income_detail', {
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

        function pintarTableCompraDetalle(lstItems) {
            let filas = ``;
            lstItems.forEach((producto) => {
                filas += `<tr>
                            <th>
                                <div class="d-flex justify-content-center gap-1">

                                    <button class="btn btn-info btn-sm btnEditItem" type="button"
                                    data-producto-id="${producto.product_id}">
                                        <i class="fas fa-edit"></i>
                                    </button>


                                    <button class="btn btn-danger btn-sm btnDeleteItem" type="button"
                                    data-producto-id="${producto.product_id}">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                </div>
                            </th>
                            <td>${producto.product_name}</td>
                            <td>${producto.type_dish_name}</td>
                            <td>${formatSoles(producto.sale_price)}</td>
                            <td>${formatSoles(producto.purchase_price)}</td>
                            <td>${producto.quantity}</td>
                        </tr>`;
            })

            const tbody = document.querySelector('#tbl_note_income_detail tbody');
            tbody.innerHTML = filas;
        }

        async function update(formCreate) {

            toastr.clear();
            const programming = @json($programming);
            if (lstNoteIncome.length === 0) {
                toastr.error('El detalle de la programación está vacío!!!');
                return;
            }

            const result = await Swal.fire({
                title: `Actualizar programación ${programming.programming_code}?`,
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
                        title: 'Actualizando programación...',
                        text: 'Por favor espere',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const formData = new FormData(formCreate);
                    formData.append('lst_detail', JSON.stringify(lstNoteIncome));
                    formData.append('_method', 'PUT')

                    const res = await axios.post(route('tenant.abastecimiento.programacion.update', {
                        id: programming.programming_id
                    }), formData);

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

        function loadDataPreview() {
            const detail = @json($detail);
            lstNoteIncome = detail;

            pintarTableCompraDetalle(lstNoteIncome);
            dtCompraDetalle = loadDataTableSimple('tbl_note_income_detail');

        }
    </script>
@endsection
