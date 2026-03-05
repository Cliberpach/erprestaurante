@extends('layouts.template')

@section('title')
    Productos
@endsection

@push('js-head')
    @vite(['resources/js/libs/filepond.js'])
@endpush

@section('content')
    @include('product.modals.mdl_create')
    @include('product.modals.mdl_edit')
    @include('product.modals.mdl_import')
    @include('utils.modals.categories.mdl_create')
    @include('utils.modals.brands.mdl_create')

    <div class="card overflow-hidden">
        <div class="card-header d-flex flex-column">

            <div class="d-flex align-items-center justify-content-between mb-2">
                <h6 class="card-title mb-0">LISTA DE PRODUCTOS</h6>

                <div class="d-flex gap-2">
                    <button class="btn btn-warning" onclick="openMdlImportProducto()">
                        <i class="fa-solid fa-upload"></i> IMPORTAR
                    </button>

                    <button type="button" class="btn btn-primary" onclick="openMdlCreate()">
                        <i class="fas fa-plus-circle"></i> NUEVO
                    </button>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <button class="btn btn-success" onclick="downloadExcel();">
                    <i class="fas fa-file-excel"></i> EXCEL
                </button>

                <button class="btn btn-danger" onclick="downloadPdf()">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
            </div>

        </div>

        <div class="card-body p-0 pb-2">
            <div class="table-responsive">
                @include('product.tables.tbl_list_products')
            </div>
        </div>
    </div>
@endsection


@section('js')
    <script>
        let dtProducts = null;

        document.addEventListener('DOMContentLoaded', () => {
            loadDtProducts();
            loadTomSelect();
            events();
        })

        function events() {
            eventsMdlCreateProduct();
            eventsMdlEditProduct();
            eventsMdlImportarProductos();
            eventsMdlCategory();
            eventsMdlBrand();
        }

        function loadDtProducts() {
            const urlGetProducts = '{{ route('tenant.inventario.productos.get-all') }}';

            dtProducts = new DataTable('#table-products', {
                serverSide: true,
                processing: true,
                responsive: true,
                ajax: {
                    url: urlGetProducts,
                    type: 'GET',
                    data: function(d) {
                        d.categoria_id = $('#categoria').val();
                        d.marca_id = $('#marca').val();
                    }
                },
                order: [
                    [0, 'desc']
                ],
                autoWidth: true,
                columns: [{
                        data: 'id',
                        name: 'id',
                        searchable: false,
                        orderable: true
                    },
                    {
                        data: 'name',
                        name: 'p.name',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'category_name',
                        name: 'c.name',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'brand_name',
                        name: 'b.name',
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: 'sale_price',
                        name: 'p.sale_price',
                        className: 'text-end',
                        searchable: false,
                        orderable: true
                    },
                    {
                        data: 'purchase_price',
                        name: 'p.purchase_price',
                        className: 'text-end',
                        searchable: false,
                        orderable: true
                    },
                    {
                        data: 'stock',
                        name: 'p.stock',
                        className: 'text-end',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'stock_min',
                        name: 'stock_min',
                        className: 'text-end',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'code_factory',
                        name: 'p.code_factory',
                        searchable: true,
                        orderable: false
                    },
                    {
                        data: 'code_bar',
                        name: 'p.code_bar',
                        searchable: true,
                        orderable: false
                    },
                    {
                        data: 'unit_symbol',
                        name: 'p.unit_symbol',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'img_route',
                        name: 'img_route',
                        className: 'text-center',
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row) {
                            if (data) {
                                return `<img class="imgShowLightBox" src="/${data}" alt="Imagen" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">`;
                            } else {
                                return '<span class="text-muted">Sin imagen</span>';
                            }
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            const baseUrlEdit = `:id`;
                            urlEdit = baseUrlEdit.replace(':id', data.id);

                            return `
                            <div class="btn-group dropup">
                                <button type="button" class="dropdown-toggle btn btn-primary btn-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-solid fa-grip text-white"></i>
                                </button>
                                <ul class="dropdown-menu" style="max-height: 150px; overflow-y: auto;">
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0);" onclick="openMdlShowProducto(${data.id})">
                                            <i class="fa-solid fa-eye text-primary me-2"></i> Ver
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0);" onclick="openMdlEdit(${data.id})">
                                            <i class="fa-solid fa-pen-to-square text-warning me-2"></i> Editar
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0);" onclick="eliminarProducto(${data.id})">
                                            <i class="fa-solid fa-trash text-danger me-2"></i> Eliminar
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            `;
                        },
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                pageLength: 25,
                lengthChange: false,
                dom: '<"row mb-3"<"col-md-6 d-flex align-items-center"f>>t<"row"<"col-6"i><"col-6"p>>',

                language: {
                    "lengthMenu": "Mostrar _MENU_ productos por página",
                    "zeroRecords": "No se encontraron resultados",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ productos",
                    "infoEmpty": "Mostrando 0 a 0 de 0 productos",
                    "infoFiltered": "(filtrado de _MAX_ productos totales)",
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

            const inputSearchDataTable = document.querySelector('#dt-search-0');
            if (inputSearchDataTable) {
                inputSearchDataTable.style.width = '500px';
                inputSearchDataTable.style.height = '50px';
                inputSearchDataTable.style.textAlign = 'left';
                inputSearchDataTable.placeholder = 'Buscar producto...';

                const previousSibling = inputSearchDataTable.previousElementSibling;
                if (previousSibling) {
                    previousSibling.style.display = 'none';
                }
            }
        }

        function loadTomSelect() {
            const categorySelect = document.getElementById('category_id');
            if (categorySelect && !categorySelect.tomselect) {
                window.categorySelect = new TomSelect(categorySelect, {
                    valueField: 'id',
                    labelField: 'description',
                    searchField: ['description', 'id'],
                    placeholder: 'Seleccionar',
                    create: false,
                    sortField: {
                        field: 'id',
                        direction: 'desc'
                    },
                    plugins: ['clear_button'],
                    render: {
                        option: (item, escape) => `
                        <div>
                            <i class="fas fa-tags" style="margin-right:6px; color:#28a745;"></i>
                            ${escape(item.description)}
                        </div>
                    `,
                        item: (item, escape) => `
                        <div>
                            <i class="fas fa-tags" style="margin-right:6px; color:#28a745;"></i>
                            ${escape(item.description)}
                        </div>
                    `
                    }
                });
            }

            const brandSelect = document.getElementById('brand_id');
            if (brandSelect && !brandSelect.tomselect) {
                window.brandSelect = new TomSelect(brandSelect, {
                    valueField: 'id',
                    labelField: 'description',
                    searchField: ['description', 'id'],
                    placeholder: 'Seleccionar',
                    create: false,
                    sortField: {
                        field: 'id',
                        direction: 'desc'
                    },
                    plugins: ['clear_button'],
                    render: {
                        option: (item, escape) => `
                        <div style="text-align:start;">
                            <i class="fas fa-bullseye" style="margin-right:6px; color:#0d6efd;"></i>
                            ${escape(item.description)}
                        </div>
                    `,
                        item: (item, escape) => `
                        <div style="text-align:start;">
                            <i class="fas fa-bullseye" style="margin-right:6px; color:#0d6efd;"></i>
                            ${escape(item.description)}
                        </div>
                    `,
                    }
                });
            }

            const unitSelect = document.getElementById('unit_id');
            if (unitSelect && !unitSelect.tomselect) {
                window.unitSelect = new TomSelect(unitSelect, {
                    valueField: 'id',
                    labelField: 'description',
                    searchField: ['description', 'id'],
                    placeholder: 'Seleccionar',
                    create: false,
                    sortField: {
                        field: 'id',
                        direction: 'desc'
                    },
                    plugins: ['clear_button'],
                    render: {
                        option: (item, escape) => `
                            <div class="d-flex align-items-center">
                                <i class="fas fa-ruler-combined me-2 text-primary"></i>
                                ${escape(item.description)}
                            </div>
                        `,
                        item: (item, escape) => `
                            <div class="d-flex align-items-center">
                                <i class="fas fa-ruler-combined me-2 text-primary"></i>
                                ${escape(item.description)}
                            </div>
                        `
                    }
                });
            }

        }

        function eliminarProducto(id) {
            toastr.clear();
            let row = getRowById(dtProducts, id);
            let message = '';
            let tipo_documento = '';

            message = `Desea eliminar el producto: ${row.name}`;

            Swal.fire({
                title: message,
                text: "Operación no reversible!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar!",
                cancelButtonText: "No, cancelar!",
                reverseButtons: true
            }).then(async (result) => {
                if (result.isConfirmed) {

                    Swal.fire({
                        title: 'Cargando...',
                        html: 'Eliminando producto...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    try {
                        let url = `{{ route('tenant.inventario.productos.destroy', ['id' => ':id']) }}`;
                        url = url.replace(':id', id);
                        const token = document.querySelector('input[name="_token"]').value;

                        const response = await fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': token
                            }
                        });

                        const res = await response.json();

                        if (res.success) {
                            dtProducts.ajax.reload();
                            toastr.success(res.message, 'OPERACIÓN COMPLETADA');
                        } else {
                            toastr.error(res.message, 'ERROR EN EL SERVIDOR AL ELIMINAR PRODUCTO');
                        }

                    } catch (error) {
                        toastr.error(error, 'ERROR EN LA PETICIÓN ELIMINAR PRODUCTO');
                    } finally {
                        Swal.close();
                    }

                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    Swal.fire({
                        title: "Operación cancelada",
                        text: "No se realizaron acciones",
                        icon: "error"
                    });
                }
            });
        }

        function exportarExcelProductos() {
            const categoriaId = document.getElementById('categoria').value;
            const marcaId = document.getElementById('marca').value;

            const url = '{{ route('tenant.inventario.productos.export-excel') }}' +
                `?categoriaId=${categoriaId}&marcaId=${marcaId}`;

            window.location.href = url;
        }

        function downloadExcel() {

            const url = @json(route('tenant.inventario.productos.excel'));

            const params = {};

            const queryString = new URLSearchParams(params).toString();

            const finalUrl = `${url}?${queryString}`;
            window.location.href = finalUrl;

        }

        function downloadPdf() {

            const url = @json(route('tenant.inventario.productos.pdf'));

            const params = {};

            const queryString = new URLSearchParams(params).toString();

            const finalUrl = `${url}?${queryString}`;
            window.open(finalUrl, '_blank');

        }
    </script>
@endsection
