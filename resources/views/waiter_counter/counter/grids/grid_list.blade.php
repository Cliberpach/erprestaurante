<div class="row mb-3">
    <div class="col-12">

        <div class="btn-group w-100" role="group" id="table-filters">

            <button type="button"
                class="btn btn-light active filter-btn"
                data-filter="all">
                Todas
            </button>

            <button type="button"
                class="btn btn-light filter-btn"
                data-filter="free">
                🔵 Libres
            </button>

            <button type="button"
                class="btn btn-light filter-btn"
                data-filter="occupied">
                🔴 Ocupadas
            </button>

        </div>

    </div>
</div>

<div class="container-fluid">
    <div id="tables-grid" class="row g-4"></div>
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


<script>
    function eventsGridList() {
        const buttons = document.querySelectorAll('#table-filters button');

        buttons.forEach(button => {

            button.addEventListener('click', () => {

                buttons.forEach(btn => btn.classList.remove('active'));

                button.classList.add('active');

                const filter = button.dataset.filter;

                filterTables(filter);
            });

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
</script>
