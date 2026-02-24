<div class="row mb-3">
    <div class="quick-table-access">
        <input type="text" id="table-number-jump" class="form-control input-fill" placeholder="Mesa #">
    </div>
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

<div id="swipe-hint" class="swipe-hint">
    <span>Desliza para navegar</span>
    <br>
    <span style="text-align: center;">⟵ ⟶</span>
</div>

<style>
    #circles-container {
        position: relative;
    }
</style>

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

<style>
    .swipe-hint {
        position: absolute;
        /* ⭐ ya no fixed */
        top: 15px;
        /* ⭐ visible SIEMPRE */
        left: 50%;
        transform: translateX(-50%);

        background: rgba(0, 0, 0, 0.65);
        color: white;

        padding: 6px 14px;
        border-radius: 18px;

        font-size: 13px;
        font-weight: 500;

        opacity: 0;
        pointer-events: none;

        transition: opacity .25s ease, transform .25s ease;
        z-index: 10;
    }

    .swipe-hint.show {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }
</style>
<style>
    .quick-table-access {
        position: fixed;
        top: 140px;
        left: 50%;
        transform: translateX(-50%);
        width: 110px;
        z-index: 9999;
    }

    .mostrador-container {
        position: relative;
    }
</style>
<style>
    #table-number-jump {
        height: 38px;
        font-size: 16px;
        text-align: center;
        font-weight: 600;
    }

    .table-card.table-highlight {

        background: #ffc107 !important;
        /* 🔥 forzamos */
        color: #000 !important;

        transform: scale(1.15);

        box-shadow: 0 0 0 4px rgba(255, 193, 7, 0.9),
            0 10px 25px rgba(0, 0, 0, 0.35);

        transition: all 0.2s ease;
    }

    @keyframes tableFlash {

        0% {
            background-color: inherit;
        }

        40% {
            background-color: #ffc107;
        }

        /* 🔥 flash */
        100% {
            background-color: inherit;
        }
    }
</style>

<script>
    let circlesTables = null;
    let tablePage = 0;
    const tableLength = 20;

    let touchStartX = 0;
    let touchStartY = 0;
    let touchEndX = 0;
    let touchEndY = 0;

    let tableSearchTimer;

    function eventsGridList() {

        const buttons = document.querySelectorAll('#table-filters button');
        buttons.forEach(button => {
            button.addEventListener('click', () => {
                actionBtnFilter(button, buttons);
            });
        });

        document.getElementById('nextPage').addEventListener('click', () => {
            actionNextPage();
        });

        document.getElementById('prevPage').addEventListener('click', () => {
            actionPrevPage();
        });


        const area = document.getElementById('circles-container');
        area.addEventListener('touchstart', e => {
            actionTouchStart(e);
        });

        area.addEventListener('touchend', e => {
            actionTouchend(e);
        });

        document.getElementById('table-number-jump').addEventListener('input', e => {
            clearTimeout(tableSearchTimer);
            tableSearchTimer = setTimeout(() => {
                actionIputTableSearch(e);
            }, 1000);
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

        const diffX = touchEndX - touchStartX;
        const diffY = touchEndY - touchStartY;

        // ⭐⭐⭐ CLAVE → si el gesto es más vertical → IGNORAR
        if (Math.abs(diffY) > Math.abs(diffX)) return;

        if (Math.abs(diffX) < 80) return; // umbral más realista para tablet 👌

        if (diffX < 0) {
            nextPage();
        } else {
            prevPage();
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

    function actionBtnFilter(button, buttons) {
        buttons.forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');
        tablePage = 0;
        loadTablesAsCircles();
    }

    function actionNextPage() {
        const total = circlesTables.recordsFiltered;

        if ((tablePage + 1) * tableLength >= total) return;

        tablePage++;
        loadTablesAsCircles();
    }

    function actionPrevPage() {
        if (tablePage === 0) return;

        tablePage--;
        loadTablesAsCircles();
    }

    function actionTouchStart(e) {
        touchStartX = e.changedTouches[0].screenX;
        touchStartY = e.changedTouches[0].screenY;
    }

    function actionTouchend(e) {
        touchEndX = e.changedTouches[0].screenX;
        touchEndY = e.changedTouches[0].screenY;

        handleSwipe();
    }

    function showSwipeHint() {
        const hint = document.getElementById('swipe-hint');

        hint.classList.add('show');

        setTimeout(() => {
            hint.classList.remove('show');
        }, 6000);
    }

    function actionIputTableSearch(e) {
        const tableNumber = parseInt(e.target.value);

        if (!tableNumber) return;

        const index = circlesTables.data.findIndex(t => t.table_name == tableNumber);

        if (index === -1) return;

        const targetPage = Math.floor(index / tableLength);

        if (targetPage !== tablePage) {
            tablePage = targetPage;
            loadTablesAsCircles();
        }

        const tableIdSearched   =   circlesTables.data[index].table_id;
        highlightTable(tableIdSearched);
    }

    function highlightTable(tableIdSearched) {

        const tableEl = document.querySelector(
            `.table-card[data-table="${tableIdSearched}"]`
        );

        if (!tableEl) return;

        // ⭐⭐⭐ 1️⃣ Scroll automático elegante
        tableEl.scrollIntoView({
            behavior: 'smooth',
            block: 'center', // 👈 MUY IMPORTANTE
            inline: 'center'
        });

        // ⭐⭐⭐ 2️⃣ Esperamos que termine el scroll
        setTimeout(() => {

            tableEl.classList.add('table-highlight');

            setTimeout(() => {
                tableEl.classList.remove('table-highlight');
            }, 700);

        }, 300); // 👈 delay fino UX 👌
    }
</script>
