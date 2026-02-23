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
            eventsGridList();
        }

        function toOrderCreate(tableId) {
            window.location.href = route('tenant.mostrador_mesero.mostrador.create', {
                table: tableId
            });
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

    </script>
@endsection
