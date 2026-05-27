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

    /* ── Dark Mode ──────────────────────────────────────────── */

    /* DataTables wrappers */
    [data-theme="dark"] .dataTables_wrapper {
        color: #cdd2e8;
    }

    [data-theme="dark"] .dataTables_filter {
        background: #161b2e;
        padding: .35rem .6rem;
    }

    [data-theme="dark"] .dataTables_filter label {
        color: #9ba3c8;
        font-size: .73rem;
    }

    [data-theme="dark"] .dataTables_filter input {
        background: #1e2235 !important;
        border-color: #3a3f5a !important;
        color: #cdd2e8 !important;
    }

    [data-theme="dark"] .dataTables_filter input::placeholder {
        color: #5a6080;
    }

    [data-theme="dark"] .dataTables_scrollHead {
        background: #1a1f36 !important;
        border-bottom-color: #2e3455 !important;
    }

    [data-theme="dark"] .dataTables_scrollHeadInner,
    [data-theme="dark"] .dataTables_scrollHeadInner table {
        background: #1a1f36 !important;
    }

    [data-theme="dark"] .dataTables_scrollBody {
        background: #12162a;
    }

    [data-theme="dark"] .dataTables_info {
        color: #7a82aa;
        font-size: .7rem;
    }

    [data-theme="dark"] .dataTables_paginate .paginate_button {
        color: #9ba3c8 !important;
    }

    [data-theme="dark"] .dataTables_paginate .paginate_button.current {
        background: #2e3455 !important;
        border-color: #3a3f5a !important;
        color: #cdd2e8 !important;
    }

    [data-theme="dark"] .dataTables_processing {
        background: #1e2235;
        color: #9ba3c8;
    }

    /* Tabla */
    [data-theme="dark"] .notif-table thead th {
        background: #1a1f36 !important;
        color: #9ba3c8;
        border-bottom-color: #2e3455;
    }

    [data-theme="dark"] .notif-table tbody td {
        color: #cdd2e8;
        border-color: #2a2f4a;
        background: #12162a;
    }

    [data-theme="dark"] .notif-table tbody tr {
        border-color: #2a2f4a;
    }

    [data-theme="dark"] .notif-row:hover {
        background: #1e2745 !important;
    }

    [data-theme="dark"] .notif-row:hover td {
        background: #1e2745 !important;
    }

    [data-theme="dark"] .notif-row.notif-selected td {
        background: rgba(13, 202, 240, .08) !important;
        box-shadow: inset 3px 0 0 #0aa2c0;
    }

    [data-theme="dark"] .notif-title {
        color: #e2e6f3;
    }

    [data-theme="dark"] .notif-sub {
        color: #7a82aa;
    }

    [data-theme="dark"] .notif-badge-pend {
        background: #3a2e0a;
        color: #ffc107;
    }

    [data-theme="dark"] .notif-badge-parc {
        background: #062a38;
        color: #22d3ee;
    }

    [data-theme="dark"] .notif-badge-cred {
        background: #0a2a1a;
        color: #34d399;
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
