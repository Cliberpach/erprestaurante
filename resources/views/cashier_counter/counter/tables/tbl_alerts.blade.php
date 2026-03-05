<style>
    /* ── Badges ────────────────────────────────────────── */
    .notif-badge {
        font-size: .63rem;
        padding: .22em .5em;
        border-radius: .3rem;
        font-weight: 600;
    }

    .notif-badge-pend {
        background: #fff3cd;
        color: #856404;
    }

    .notif-badge-parc {
        background: #cff4fc;
        color: #055160;
    }

    .notif-badge-cred {
        background: #d1e7dd;
        color: #0a3622;
    }


    /*TABLE*/
    .notif-table thead th {
        font-size: .7rem;
        font-weight: 700;
        color: #495057;
        background: #f5f5f5 !important;
        border-bottom: 2px solid #dee2e6;
        padding: .4rem .45rem;
        position: sticky;
        top: 0;
        z-index: 1;
        white-space: nowrap;
    }

    .notif-table tbody td {
        padding: .38rem .45rem;
        font-size: .76rem;
    }

    .notif-row {
        cursor: pointer;
        transition: background .12s;
    }

    .notif-row:hover {
        background: #f0fbff !important;
    }

    .notif-row.notif-selected {
        background: rgba(13, 202, 240, .11) !important;
        box-shadow: inset 3px 0 0 #0aa2c0;
    }

    .notif-title {
        font-weight: 600;
        font-size: .76rem;
        line-height: 1.2;
    }

    .notif-sub {
        font-size: .65rem;
    }
</style>

<table class="table-hover table-sm notif-table dt-alerts mb-0 table">
    <thead>
        <tr>
            <th style="width:36px;" class="text-center">
                Seleccionar
            </th>
            <th>Detalle</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody id="notif-tbody">

    </tbody>
</table>
