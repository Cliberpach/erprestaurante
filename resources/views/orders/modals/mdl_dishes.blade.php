<div class="modal fade" id="mdlDishes" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                            class="form-control" onchange="dtDishes.ajax.reload();">
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
                            @include('orders.tables.tbl_dishes')
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
    const lstTableDishes = [];
    const dishSelected = {
        id: null,
        name: null,
        type_name: null,
        purchase_price: null,
        sale_price: null,
        type_item: null,
        quantity: null
    }

    function eventsMdlDishes() {
        loadDtDishes();
        loadSelectsMdlDishes();
    }

    function openMdlDishes() {
        $('#mdlDishes').modal('show');
    }

    function loadSelectsMdlDishes() {
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

    function loadDtDishes() {
        const urlGetProductos = @json(route('tenant.utils.getDishesProgramming'));

        dtDishes = new DataTable('#tbl_dishes', {
            serverSide: true,
            processing: true,
            ajax: {
                url: urlGetProductos,
                type: 'GET',
                data: function(d) {
                    d.type_dish_id = $('#type_dish_id').val();
                    d.programming_id = @json($programming->id);
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
                    data: 'stock',
                    name: 'pd.stock'
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

                $(row).attr('onclick', 'selectDish(' + data.id + ')');
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

    function selectDish(producto_id) {

        const fila = getRowById(dtDishes, producto_id);

        if (!fila) {
            toastr.error('NO SE ENCONTRÓ EL PLATO EN LA TABLA PLATOS');
            return;
        }

        console.log(fila);

        //======= SETTEAR PRODUCTO =======
        const product = fila;
        document.querySelector('#producto').value = product.name;
        document.querySelector('#purchase_price').value = formatSoles(product.purchase_price);
        document.querySelector('#sale_price').value = formatSoles(product.sale_price);
        document.querySelector('#item_stock').value = product.stock;

        dishSelected.id = product.id;
        dishSelected.name = product.name;
        dishSelected.type_name = product.type_dish_name;
        dishSelected.purchase_price = product.purchase_price;
        dishSelected.sale_price = product.sale_price;
        dishSelected.stock = product.stock;
        dishSelected.type_item = 'PLATO';

        itemSelected = dishSelected;

        $('#mdlDishes').modal('hide');
        document.querySelector('#cantidad').focus();

    }

    function clearDishSelected() {

        dishSelected.id = null;
        dishSelected.name = null;
        dishSelected.type_name = null;
        dishSelected.type_item = null;
        dishSelected.quantity = null;

    }
</script>
