<div class="row mb-3">
    <div class="col-12">

        <div class="btn-group w-100" role="group" id="table-filters">

            <button type="button" class="btn btn-light active filter-btn" data-filter="all">
                Todas
            </button>

            <button type="button" class="btn btn-light filter-btn" data-filter="LIBRE">
                🔵 Libres
            </button>

            <button type="button" class="btn btn-light filter-btn" data-filter="OCUPADO">
                🔴 Ocupadas
            </button>

        </div>

    </div>
</div>

<div class="container-fluid" id="circles-container">
    <div id="tables-grid" class="row g-4">

    </div>
</div>

<div class="d-flex justify-content-center align-items-center mb-3 mt-2">

    <button class="btn btn-light me-2 px-3 shadow-sm" id="prevPage">
        ◀
    </button>

    <div class="text-muted fw-semibold px-3" id="pageInfo">
        Página 1
    </div>

    <button class="btn btn-light ms-2 px-3 shadow-sm" id="nextPage">
        ▶
    </button>

</div>

<style>
    .table-card {
        width: 100%;
        aspect-ratio: 1 / 1;
        border-radius: 50%;
        cursor: pointer;
        color: #fff;
        position: relative;
        transition: all .25s ease;
        box-shadow: 0 8px 20px rgba(0, 0, 0, .15);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
    }

    .table-card:hover {
        transform: translateY(-6px) scale(1.05);
        box-shadow: 0 14px 28px rgba(0, 0, 0, .25);
    }

    /* Número de mesa */
    .table-number {
        font-size: 1.4rem;
        font-weight: 800;
        letter-spacing: .5px;
    }

    /* Estado */
    .table-status {
        font-size: .75rem;
        margin-top: 4px;
        padding: 2px 10px;
        border-radius: 12px;
        background: rgba(255, 255, 255, .2);
    }

    /* Total */
    .table-total {
        font-size: .85rem;
        margin-top: 6px;
        font-weight: 600;
    }

    /* Estados */
    .bg-libre {
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
    }

    .bg-ocupada {
        background: linear-gradient(135deg, #dc3545, #a71d2a);
    }

    .bg-cerrada {
        background: linear-gradient(135deg, #dc3545, #a71d2a);
    }

    /* Ícono */
    .table-icon {
        font-size: 1.4rem;
        margin-bottom: 6px;
        opacity: .9;
    }
</style>


<style>
    #table-filters {
        background: #f8f9fa;
        padding: 4px;
        border-radius: 12px;
    }

    #table-filters .btn {
        border: none;
        border-radius: 10px;
        padding: 8px 16px;
        font-weight: 500;
        color: #6c757d;
        transition: all 0.2s ease;
    }

    #table-filters .btn:hover {
        background: #e9ecef;
    }

    #table-filters .btn.active {
        background: #212529;
        color: white;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }

    .filter-btn {
        min-width: 110px;
        /* ideal para tablet */
    }
</style>

<style>
    #prevPage,
    #nextPage {
        border-radius: 10px;
        min-width: 48px;
        font-size: 18px;
    }

    button:disabled {
        opacity: 0.4;
    }
</style>

<script>
    let circlesTables = null;
    let tablePage = 0;
    const tableLength = 16;

    let touchStartX = 0;
    let touchEndX = 0;

    function eventsGridList() {

        const buttons = document.querySelectorAll('#table-filters button');
        buttons.forEach(button => {
            button.addEventListener('click', () => {
                buttons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
                tablePage = 0;
                loadTablesAsCircles();
            });

        });

        document.getElementById('nextPage').addEventListener('click', () => {

            const total = circlesTables.recordsFiltered;

            if ((tablePage + 1) * tableLength >= total) return;

            tablePage++;
            loadTablesAsCircles();
        });

        document.getElementById('prevPage').addEventListener('click', () => {

            if (tablePage === 0) return;

            tablePage--;
            loadTablesAsCircles();
        });


        const area = document.getElementById('circles-container');
        area.addEventListener('touchstart', e => {
            touchStartX = e.changedTouches[0].screenX;
        });

        area.addEventListener('touchend', e => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        });
    }

    function filterTables(filter) {

        document.querySelectorAll('.table-circle').forEach(table => {

            if (filter === 'all') {
                table.classList.remove('d-none');
                return;
            }

            if (table.dataset.status === filter) {
                table.classList.remove('d-none');
            } else {
                table.classList.add('d-none');
            }

        });

    }

    async function loadTablesAsCircles() {
        try {
            mostrarAnimacion1();
            const activeFilter = document.querySelector('#table-filters .active');
            const status = activeFilter.dataset.filter;

            const res = await axios.get(route('tenant.mostrador_mesero.mostrador.getAll'), {
                params: {
                    start: tablePage * tableLength,
                    length: tableLength,
                    status: status
                }
            });

            ocultarAnimacion1();

            if (res.data.success) {
                circlesTables = res.data;
                paintCirclesTables(circlesTables.data);
                updatePageInfo();
                updateButtons();

            } else {
                toastr.error(res.data.message);
            }

        } catch (error) {
            console.error(error);
            toastr.error('Error al cargar las mesas');
        }
    }

    function paintCirclesTables(data) {
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
    }

    function updatePageInfo() {

        const totalPages = Math.ceil(circlesTables.recordsFiltered / tableLength);

        document.getElementById('pageInfo').innerText =
            `Página ${tablePage + 1} / ${totalPages}`;
    }

    function updateButtons() {

        document.getElementById('prevPage').disabled = tablePage === 0;

        const total = circlesTables.recordsFiltered;

        document.getElementById('nextPage').disabled =
            (tablePage + 1) * tableLength >= total;
    }


    function handleSwipe() {

        const swipeDistance = touchEndX - touchStartX;

        if (Math.abs(swipeDistance) < 60) return; // ⭐ filtro anti-movimientos pequeños

        if (swipeDistance < 0) {
            nextPage(); // ← swipe izquierda
        } else {
            prevPage(); // ← swipe derecha
        }
    }


    function nextPage() {

        const total = circlesTables.recordsFiltered;

        if ((tablePage + 1) * tableLength >= total) return;

        tablePage++;
        loadTablesAsCircles();
    }

    function prevPage() {

        if (tablePage === 0) return;

        tablePage--;
        loadTablesAsCircles();
    }
</script>
