@extends('layouts.template')

@section('title')
    Mostrador Mozo
@endsection

@section('content')
    @include('waiter_counter.counter.modals.mdl_show')
    @include('waiter_counter.counter.modals.mdl_change_table')
    @include('waiter_counter.counter.modals.mdl_delete_order')
    <div class="card overflow-hidden">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="card-title mb-0">Mostrador Mozo</h6>
            <div class="d-flex flex-wrap gap-2">
                <div class="btn-group btn-group-sm" role="group">
                    <button id="btn-view-circles" class="btn btn-primary active" onclick="setView('circles')">
                        <i class="fas fa-sync-alt me-1"></i> Refrescar mostrador
                    </button>
                </div>

            </div>
        </div>
        <div class="card-body p-0 pb-2">

            <!-- Vista Círculos -->
            <div id="view-circles">
                @include('waiter_counter.counter.grids.grid_list')
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
        let dtItems = null;
        let currentView = 'circles';

        document.addEventListener('DOMContentLoaded', () => {
            setView('circles');
            loadTablesAsCircles();
            events();
        })

        function events() {
            eventsMdlOrderShow();
            eventsMdlChangeTbl();
            eventsMdlDeleteOrder();
        }

        function toOrderCreate(tableId) {
            window.location.href = route('tenant.mostrador_mesero.mostrador.create', {
                table: tableId
            });
        }

        async function loadTablesAsCircles() {
            try {
                mostrarAnimacion1();
                const res = await axios.get(route('tenant.mostrador_mesero.mostrador.getAll'));
                ocultarAnimacion1();
                const data = res.data.data ?? res.data;
                const grid = document.getElementById('tables-grid');
                grid.innerHTML = '';

                data.forEach(item => {

                    let bgClass = 'bg-libre';
                    let statusText = 'LIBRE';

                    if (item.status === 'OCUPADO') {
                        bgClass = 'bg-ocupada';
                        statusText = 'OCUPADO';
                    } else if (!item.status) {
                        bgClass = 'bg-cerrada';
                        statusText = 'LIBRE';
                    }

                    grid.innerHTML += `
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                            <div class="table-card ${bgClass}"
                                data-table="${item.table_id}"
                                data-order = "${item.order_id}"
                                data-status="${item.status ?? ''}"
                                style="cursor:pointer">

                                <div class="table-icon">
                                    <i class="fas fa-utensils"></i>
                                </div>

                                <div class="table-number">
                                    ${item.table_name}
                                </div>

                                <div class="table-status">
                                    ${statusText}
                                </div>

                                ${item.total ? `
                                                    <div class="table-total">
                                                        S/ ${formatSoles(item.total)}
                                                    </div>
                                                    ` : ''}
                            </div>
                        </div>
                    `;

                    if (!grid.dataset.delegateAttached) {
                        grid.addEventListener('click', (e) => {
                            const card = e.target.closest('.table-card');
                            if (!card || !grid.contains(card)) return;

                            const tableId = card.getAttribute('data-table');
                            const status = (card.getAttribute('data-status') || '').toString()
                                .toUpperCase();
                            const orderId = card.getAttribute('data-order');

                            if (status === 'LIBRE' || !status) {
                                toOrderCreate(tableId);
                            } else {
                                openMdlShowOrder(tableId, orderId);
                            }
                        });

                        grid.dataset.delegateAttached = '1';
                    }
                });

            } catch (error) {
                console.error(error);
                toastr.error('Error al cargar las mesas');
            }
        }

        function setView(view) {
            localStorage.setItem('mostrador_view', view);

            const circles = document.getElementById('view-circles');

            const btnCircles = document.getElementById('btn-view-circles');

            if (view === 'circles') {
                circles.classList.remove('d-none');

                btnCircles.classList.add('btn-primary', 'active');
                btnCircles.classList.remove('btn-outline-primary');

                loadTablesAsCircles();
            }
        }

        function eliminar(id) {
            const fila = getRowById(dtItems, id);
            const htmlInfo = `
                <div class="card shadow-sm border-0">
                    <div class="card-body p-2" style="font-size: 1.2rem;">

                        <div class="mb-1">
                            <i class="fas fa-user text-primary me-1 small"></i>
                            <span class="fw-bold small">Nombre:</span><br>
                            <span class="text-muted small">${fila.name}</span>
                        </div>

                        <div class="mb-1">
                            <i class="fas fa-utensils text-info me-1 small"></i>
                            <span class="fw-bold small">Tipo:</span><br>
                            <span class="text-muted small">${fila.type_dish_name}</span>
                        </div>

                        <div class="mb-1">
                            <i class="fas fa-flag text-success me-1 small"></i>
                            <span class="fw-bold small">P.Costo:</span><br>
                            <span class="text-muted small">${formatSoles(fila.purchase_price)}</span>
                        </div>

                        <div class="mb-1">
                            <i class="fas fa-tag text-warning me-1 small"></i>
                            <span class="fw-bold small">P.Venta:</span><br>
                            <span class="text-muted small">${formatSoles(fila.sale_price)}</span>
                        </div>
                    </div>
                </div>
            `;

            Swal.fire({
                title: '¿Desea eliminar el plato?',
                html: `${htmlInfo}`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'No, cancelar',
                focusCancel: true,
                reverseButtons: true
            }).then(async (result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Eliminando plato...',
                        html: `
                            <div style="display:flex; align-items:center; justify-content:center; flex-direction:column;">
                                <i class="fa fa-spinner fa-spin fa-3x text-primary mb-3"></i>
                                <p style="margin:0; font-weight:600;">Por favor, espere un momento</p>
                            </div>
                        `,
                        allowOutsideClick: false,
                        showConfirmButton: false
                    });

                    try {
                        const res = await axios.delete(route('tenant.abastecimiento.platos.destroy', id));
                        if (res.data.success) {
                            toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                            dtItems.ajax.reload();
                        } else {
                            toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                        }
                    } catch (error) {
                        toastr.error(error, 'ERROR EN LA PETICIÓN ELIMINAR PLATO');
                    } finally {
                        Swal.close();
                    }

                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: 'Cancelado',
                        text: 'La solicitud ha sido cancelada.',
                        icon: 'error',
                        confirmButtonText: 'Entendido',
                        customClass: {
                            confirmButton: 'btn btn-secondary'
                        },
                        buttonsStyling: false
                    });
                }
            });
        }
    </script>
@endsection
