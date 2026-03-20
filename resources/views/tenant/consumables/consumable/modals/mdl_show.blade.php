<div class="modal fade" id="mdlShowConsumable" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    VER INSUMO <span style="padding:0;margin:0;" id="spanNoteId"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="col-12 bg-light mb-2 rounded border p-3 shadow-sm">
                    <div class="row g-2">

                        <div class="col-12 col-lg-6">
                            <p class="mb-1">
                                <i class="fas fa-box text-primary me-2"></i>
                                <strong>Nombre:</strong>
                                <span id="name_mdlShow"></span>
                            </p>
                        </div>

                        <div class="col-12 col-lg-6">
                            <p class="mb-1">
                                <i class="fas fa-tags text-success me-2"></i>
                                <strong>Categoría:</strong>
                                <span id="categoryName_mdlShow"></span>
                            </p>
                        </div>

                        <div class="col-12 col-lg-6">
                            <p class="mb-1">
                                <i class="fas fa-copyright text-info me-2"></i>
                                <strong>Marca:</strong>
                                <span id="brandName_mdlShow"></span>
                            </p>
                        </div>

                        <div class="col-12 col-lg-6">
                            <p class="mb-1">
                                <i class="fas fa-balance-scale text-warning me-2"></i>
                                <strong>Unidad:</strong>
                                <span id="unit_mdlShow"></span>
                            </p>
                        </div>

                        <div class="col-12 col-lg-6">
                            <p class="mb-1">
                                <i class="fas fa-dollar-sign text-success me-2"></i>
                                <strong>Compra:</strong>
                                <span id="purchasePrice_mdlShow"></span>
                            </p>
                        </div>

                        <div class="col-12 col-lg-6">
                            <p class="mb-1">
                                <i class="fas fa-cash-register text-success me-2"></i>
                                <strong>Venta:</strong>
                                <span id="salePrice_mdlShow"></span>
                            </p>
                        </div>

                        <div class="col-12 col-lg-6">
                            <p class="mb-1">
                                <i class="fas fa-warehouse text-danger me-2"></i>
                                <strong>Stock:</strong>
                                <span id="stock_mdlShow"></span>
                            </p>
                        </div>

                        <div class="col-12 col-lg-6">
                            <p class="mb-1">
                                <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                                <strong>Stock Min:</strong>
                                <span id="stockMin_mdlShow"></span>
                            </p>
                        </div>

                        <div class="col-12 col-lg-6">
                            <p class="mb-1">
                                <i class="fas fa-barcode text-dark me-2"></i>
                                <strong>Código Barra:</strong>
                                <span id="codeBar_mdlShow"></span>
                            </p>
                        </div>

                        <div class="col-12 col-lg-6">
                            <p class="mb-1">
                                <i class="fas fa-industry text-secondary me-2"></i>
                                <strong>Código Fábrica:</strong>
                                <span id="codeFactory_mdlShow"></span>
                            </p>
                        </div>

                        <div class="col-12">
                            <p class="mb-1">
                                <i class="fas fa-align-left text-muted me-2"></i>
                                <strong>Descripción:</strong>
                                <span id="description_mdlShow"></span>
                            </p>
                        </div>

                        <!-- Imagen -->
                        <div class="col-12 mt-2 text-center">
                            <img id="imgConsumable_mdlShow" class="img-fluid rounded shadow-sm"
                                style="max-height: 200px;">
                        </div>

                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Salir</button>
            </div>
        </div>
    </div>
</div>

@push('js-script')
    <script>
        let dtConsumablesShow = null;

        function eventsMdlConsumableShow() {
            document.getElementById('mdlShowConsumable').addEventListener('hidden.bs.modal', () => {
                clearMdlConsumableShow();
            });
        }

        function openMdlConsumableShow(consumableId) {
            getConsumable(consumableId);
        }

        async function getConsumable(consumableId) {
            try {
                toastr.clear();
                mostrarAnimacion1();
                const token = document.querySelector('input[name="_token"]').value;
                const urlgetConsumable = @json(route('tenant.insumos.insumos.getOne', ['id' => 'ID']));
                const url = urlgetConsumable.replace('ID', consumableId);

                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': token
                    }
                });

                const res = await response.json();

                if (res.success) {

                    paintConsumableShow(res.data);

                    //dtConsumablesShow = destroyDataTable(dtConsumablesShow);
                    //clearTable('tbl_purchase_document_show');
                    //paintConsumableShowDetail(res.data.detail);
                    //dtConsumablesShow = loadDataTableSimple('tbl_purchase_document_show');

                    $('#mdlShowConsumable').modal('show');
                    toastr.success('MOSTRANDO INSUMO');
                } else {
                    toastr.error(res.message, 'ERROR EN EL SERVIDOR');
                }

            } catch (error) {
                toastr.error(error, 'ERROR EN LA PETICIÓN VER INSUMO');
            } finally {
                ocultarAnimacion1();
            }
        }

        function paintConsumableShow(consumable) {
            document.getElementById('name_mdlShow').textContent = consumable.name;
            document.getElementById('categoryName_mdlShow').textContent = consumable.category_name;
            document.getElementById('brandName_mdlShow').textContent = consumable.brand_name;

            document.getElementById('unit_mdlShow').textContent =
                `${consumable.unit_name} (${consumable.unit_symbol})`;

            document.getElementById('purchasePrice_mdlShow').textContent = formatSoles(consumable.purchase_price);

            document.getElementById('salePrice_mdlShow').textContent = formatSoles(consumable.sale_price);

            document.getElementById('stock_mdlShow').textContent = consumable.stock;

            document.getElementById('stockMin_mdlShow').textContent = consumable.stock_min;

            document.getElementById('codeBar_mdlShow').textContent =
                consumable.code_bar ?? '-';

            document.getElementById('codeFactory_mdlShow').textContent =
                consumable.code_factory ?? '-';

            document.getElementById('description_mdlShow').textContent =
                consumable.description || '-';

            document.getElementById('imgConsumable_mdlShow').src =
                consumable.img_route ?? '';
        }

        function paintConsumableShowDetail(details) {

            const tbody = document.querySelector("#tbl_purchase_document_show tbody");

            details.forEach(detail => {
                const row = document.createElement("tr");

                row.innerHTML = `
                <td>${detail.product_name}</td>
                <td>${detail.category_name}</td>
                <td>${detail.brand_name}</td>
                <td>${parseFloat(detail.quantity).toFixed(2)}</td>
                <td>${parseFloat(detail.purchase_price).toFixed(2)}</td>
                <td>${parseFloat(detail.subtotal).toFixed(2)}</td>
            `;

                tbody.appendChild(row);
            });
        }

        function clearMdlConsumableShow() {
            const campos = [
                'name_mdlShow',
                'categoryName_mdlShow',
                'brandName_mdlShow',
                'unit_mdlShow',
                'purchasePrice_mdlShow',
                'salePrice_mdlShow',
                'stock_mdlShow',
                'stockMin_mdlShow',
                'codeBar_mdlShow',
                'codeFactory_mdlShow',
                'description_mdlShow'
            ];

            campos.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.textContent = '';
            });

            const img = document.getElementById('imgConsumable_mdlShow');
            if (img) {
                img.src = '';
                img.alt = '';
            }
        }
    </script>
@endpush
