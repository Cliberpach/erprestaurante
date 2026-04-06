<div class="modal fade" id="mdlConsumables" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Seleccionar Insumo</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                        <label for="category_mdlconsumables" style="font-weight: bold;">CATEGORÍA</label>

                        <select data-placeholder="Seleccione una opción" name="category_mdlconsumables"
                            id="category_mdlconsumables" class=""
                            onchange="paramsMdlConsumable.dtConsumables.ajax.reload();">
                            <option></option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                        <label for="brand_mdlconsumables" style="font-weight: bold;">MARCA</label>

                        <select data-placeholder="Seleccione una opción" name="brand_mdlconsumables"
                            id="brand_mdlconsumables" class=""
                            onchange="paramsMdlConsumable.dtConsumables.ajax.reload();">
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
                            @include('utils.modals.mdl_select_consumable.tbl.tbl_consumables')
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
    const paramsMdlConsumable = {
        itemSelected: {
            item_id: null,
            item_name: null,
            category_name: null,
            brand_name: null,
            item_unit: null,
            quantity: null,
            purchase_price: null,
            almacen_id: null,
            unit_name: null
        },
        dtConsumables: null,
        onSelect: null
    }

    function loadMdlSelectConsumable(config) {
        paramsMdlConsumable.onSelect = config.onSelect;
        eventsMdlConsumables();
    }

    function eventsMdlConsumables() {
        loadSelectsMdlConsumables();
        loadDtConsumables();
    }

    function openMdlConsumables() {
        $('#mdlConsumables').modal('show');
    }

    function loadSelectsMdlConsumables() {

        const brandSelectMp = document.getElementById('brand_mdlconsumables');
        if (brandSelectMp && !brandSelectMp.tomselect) {
            window.brandSelectMp = new TomSelect(brandSelectMp, {
                valueField: 'id',
                labelField: 'description',
                searchField: ['description', 'id'],
                create: false,
                sortField: {
                    field: 'id',
                    direction: 'desc'
                },
                plugins: ['clear_button'],
                render: {
                    option: (item, escape) => `
                            <div>
                                ${escape(item.description)}
                            </div>
                        `,
                    item: (item, escape) => `
                            <div>${escape(item.description)}</div>
                        `
                }
            });
        }

        const categorySelectMp = document.getElementById('category_mdlconsumables');
        if (categorySelectMp && !categorySelectMp.tomselect) {
            window.categorySelectMp = new TomSelect(categorySelectMp, {
                valueField: 'id',
                labelField: 'description',
                searchField: ['description', 'id'],
                create: false,
                sortField: {
                    field: 'id',
                    direction: 'desc'
                },
                plugins: ['clear_button'],
                render: {
                    option: (item, escape) => `
                            <div>
                                ${escape(item.description)}
                            </div>
                        `,
                    item: (item, escape) => `
                            <div>${escape(item.description)}</div>
                        `
                }
            });
        }

    }

    function selectConsumable(id) {
        const fila = getRowById(paramsMdlConsumable.dtConsumables, id);

        if (!fila) {
            toastr.error('No se encontró el insumo seleccionado');
            return;
        }

        //======= SETTEAR PRODUCTO =======
        /*const producto = fila;
        document.querySelector('#producto').value = producto.name;
        document.querySelector('#precio').value = producto.purchase_price;*/

        paramsMdlConsumable.itemSelected.item_id = fila.id;
        paramsMdlConsumable.itemSelected.item_name = fila.name;
        paramsMdlConsumable.itemSelected.category_name = fila.category_name;
        paramsMdlConsumable.itemSelected.brand_name = fila.brand_name;
        paramsMdlConsumable.itemSelected.item_unit = 'NIU';
        paramsMdlConsumable.itemSelected.purchase_price = fila.purchase_price;
        paramsMdlConsumable.itemSelected.unit_name = fila.unit_name;

        const res = paramsMdlConsumable.onSelect(paramsMdlConsumable.itemSelected);
        if (res) {
            $('#mdlConsumables').modal('hide');
        }
        /*document.querySelector('#cantidad').focus();*/
    }

    function loadDtConsumables() {
        const urlGetProductos = @json(route('tenant.utils.getConsumables'));

        paramsMdlConsumable.dtConsumables = new DataTable('#tbl_consumables', {
            serverSide: true,
            processing: true,
            ajax: {
                url: urlGetProductos,
                type: 'GET',
                data: function(d) {
                    d.categoria_id = $('#category_mdlconsumables').val();
                    d.marca_id = $('#brand_mdlconsumables').val();
                },
            },
            columns: [{
                    data: 'id',
                    name: 'p.id',
                    searchable:true,
                    orderable:true
                },
                {
                    data: 'name',
                    name: 'p.name',
                    searchable:true,
                    orderable:true
                },
                {
                    data: 'category_name',
                    name: 'c.name',
                    searchable:true,
                    orderable:true
                },
                {
                    data: 'brand_name',
                    name: 'b.name',
                    searchable:true,
                    orderable:true
                },

                {
                    data: 'stock',
                    name: 'Stock',
                    searchable:false,
                    orderable:true
                }
            ],
            createdRow: function(row, data, dataIndex) {
                $(row).css('cursor', 'pointer');

                $(row).attr('onclick', 'selectConsumable(' + data.id + ')');
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

    function clearFormSelectConsumable() {
        /*const inputProducto = document.querySelector('#producto');
        const inputCantidad = document.querySelector('#cantidad');
        const inputPrecio = document.querySelector('#precio');

        inputProducto.value = '';
        inputCantidad.value = '';
        inputPrecio.value = '';*/
        paramsMdlConsumable.itemSelected.product_id = null;
        paramsMdlConsumable.itemSelected.product_name = null;
        paramsMdlConsumable.itemSelected.category_name = null;
        paramsMdlConsumable.itemSelected.brand_name = null;
        paramsMdlConsumable.itemSelected.producto_unidad_medida = null;
        paramsMdlConsumable.itemSelected.quantity = null;
        paramsMdlConsumable.itemSelected.purchase_price = null;
        //$('#almacen').val(1).trigger('change');
    }
</script>
