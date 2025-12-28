<div class="modal fade" id="mdlProductos" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Seleccionar Producto</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                        <label for="categoria" style="font-weight: bold;">CATEGORÍA</label>

                        <select data-placeholder="Seleccione una opción" name="categoria" id="categoria"
                            class="select2_form_mdl" onchange="dtProductos.ajax.reload();">
                            <option></option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                        <label for="marca" style="font-weight: bold;">MARCA</label>

                        <select data-placeholder="Seleccione una opción" name="marca" id="marca"
                            class="select2_form_mdl" onchange="dtProductos.ajax.reload();">
                            <option></option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            @include('inventory.note_income.tables.tbl_products')
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
    const productSelected = {
        id: null,
        name: null,
        type_name: null,
        purchase_price: null,
        sale_price: null,
        type_item: null,
        quantity: null,
    }

    function eventsMdlProductos() {
        loadDataTableProducts();
        loadSelectsMdlProducts();
    }

    function loadSelectsMdlProducts() {
        const categorySelect = document.getElementById('categoria');
        if (categorySelect && !categorySelect.tomselect) {
            window.categorySelect = new TomSelect(categorySelect, {
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

        const brandSelect = document.getElementById('marca');
        if (brandSelect && !brandSelect.tomselect) {
            window.brandSelect = new TomSelect(brandSelect, {
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
        const urlGetProductos = @json(route('tenant.utils.getProducts'));

        dtProductos = new DataTable('#tbl_products', {
            serverSide: true,
            processing: true,
            ajax: {
                url: urlGetProductos,
                type: 'GET',
                data: function(d) {
                    d.categoria_id = $('#categoria').val();
                    d.marca_id = $('#marca').val();
                },
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'brand_name',
                    name: 'brand_name'
                },
                {
                    data: 'category_name',
                    name: 'category_name'
                },
                {
                    data: 'stock',
                    name: 'Stock'
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

    function openMdlProducts() {
        $('#mdlProductos').modal('show');
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
        document.querySelector('#item_stock').value = product.stock;

        productSelected.id = product.id;
        productSelected.name = product.name;
        productSelected.type_name = product.category_name + '-' + product.brand_name;
        productSelected.sale_price  =   product.sale_price;
        productSelected.purchase_price  =   product.purchase_price;
        productSelected.type_item = 'PRODUCTO';

        itemSelected = productSelected;

        $('#mdlProductos').modal('hide');
        document.querySelector('#cantidad').focus();

    }

    function clearProductSelected() {

        productSelected.id = null;
        productSelected.name = null;
        productSelected.type_name = null;
        productSelected.type_item = null;
        productSelected.quantity = null;

    }
</script>
