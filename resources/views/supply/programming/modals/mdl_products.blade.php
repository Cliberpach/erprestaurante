<div class="modal fade" id="mdlProductos" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Seleccionar Plato</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                        <label for="type_dish_id" style="font-weight: bold;">TIPO PLATO</label>

                        <select data-placeholder="Seleccione una opción" name="type_dish_id" id="type_dish_id"
                            class="form-control" onchange="dtProductos.ajax.reload();">
                            <option></option>
                            @foreach ($types_dish as $type_dish)
                                <option value="{{ $type_dish->id }}">{{ $type_dish->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            @include('supply.programming.tables.tbl_dishes')
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>

            </div>
        </div>
    </div>
</div>

<script>
    const lstTableProducts = [];
    const product_selected = {
        product_id: null,
        product_name: null,
        type_dish_name: null,
        purchase_price: null,
        sale_price: null,
        quantity: null
    }

    function eventsMdlProductos() {
        loadDataTableProducts();
        loadSelectsMdlProducts();
    }

    function openMdlProducts() {
        $('#mdlProductos').modal('show');
    }

    function loadSelectsMdlProducts() {
        const typeDishSelect = document.getElementById('type_dish_id');
        if (typeDishSelect && !typeDishSelect.tomselect) {
            window.typeDishSelect = new TomSelect(typeDishSelect, {
                valueField: 'id',
                labelField: 'name',
                searchField: ['name', 'id'],
                create: false,
                sortField: {
                    field: 'id',
                    direction: 'desc'
                },
                plugins: ['clear_button'],
                render: {
                    option: (item, escape) => `
                            <div>
                                ${escape(item.name)}
                            </div>
                        `,
                    item: (item, escape) => `
                            <div>${escape(item.name)}</div>
                        `
                }
            });
        }
    }

    function loadDataTableProducts() {
        const urlGetProductos = @json(route('tenant.utils.getDisheslist'));

        dtProductos = new DataTable('#tbl_products', {
            serverSide: true,
            processing: true,
            ajax: {
                url: urlGetProductos,
                type: 'GET',
                data: function(d) {
                    d.type_dish_id = $('#type_dish_id').val();
                },
            },
            order: [
                [0, 'desc']
            ],
            columns: [{
                    data: 'id',
                    name: 'd.id'
                },
                {
                    data: 'name',
                    name: 'd.name'
                },
                {
                    data: 'type_dish_name',
                    name: 'td.name'
                },
                {
                    data: 'sale_price',
                    name: 'd.sale_price',
                    render: function(data, type, row) {
                        return formatSoles(data);
                    }
                },
                {
                    data: 'purchase_price',
                    name: 'd.purchase_price',
                    render: function(data, type, row) {
                        return formatSoles(data);
                    }
                },
                {
                    data: 'img_route',
                    name: 'd.img_route',
                    searchable: false,
                    orderable: false,
                    className: "text-center",
                    render: function(data, type, row) {

                        const ASSET_URL = @json(asset(''));

                        if (!data) {
                            return `<span class="text-muted">Sin imagen</span>`;
                        }

                        return `
                                <img
                                    src="${ASSET_URL}${data}"
                                    alt="img"
                                    style="
                                        width: 45px;
                                        height: 45px;
                                        object-fit: cover;
                                        border-radius: 6px;
                                    "
                                >
                            `;
                    }
                }
            ],
            createdRow: function(row, data, dataIndex) {
                $(row).css('cursor', 'pointer');

                $(row).attr('onclick', 'selectProduct(' + data.id + ')');
            },
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

    function selectProduct(producto_id) {

        const fila = getRowById(dtProductos, producto_id);

        if (!fila) {
            toastr.error('NO SE ENCONTRÓ EL PRODUCTO EN LA TABLA PRODUCTOS');
            return;
        }

        console.log(fila);

        //======= SETTEAR PRODUCTO =======
        const product = fila;
        document.querySelector('#producto').value = product.name;
        document.querySelector('#purchase_price').value = formatSoles(product.purchase_price);
        document.querySelector('#sale_price').value = formatSoles(product.sale_price);

        product_selected.product_id = product.id;
        product_selected.product_name = product.name;
        product_selected.type_dish_name = product.type_dish_name;
        product_selected.purchase_price = product.purchase_price;
        product_selected.sale_price = product.sale_price;

        console.log('PRODUCTO ELEGIDO');
        console.log(product_selected);


        $('#mdlProductos').modal('hide');
        document.querySelector('#cantidad').focus();

    }

    function clearFormSelectProduct() {

        const inputProducto = document.querySelector('#producto');
        const inputPurchasePrice = document.querySelector('#purchase_price');
        const inputSalePrice = document.querySelector('#sale_price');

        const inputCantidad = document.querySelector('#cantidad');

        inputProducto.value = '';
        inputPurchasePrice.value = '';
        inputSalePrice.value = '';
        inputCantidad.value = '';

        product_selected.product_id = null;
        product_selected.product_name = null;
        product_selected.category_name = null;
        product_selected.brand_name = null;
        product_selected.quantity = null;

    }
</script>
