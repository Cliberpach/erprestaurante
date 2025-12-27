<div class="row mb-3">
    <div class="col-md-4 col-sm-6">
        <input type="text" id="table-search" class="form-control form-control-sm"
            placeholder="🔍 Buscar mesa, cliente, código...">
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
