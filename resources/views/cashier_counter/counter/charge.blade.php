@extends('layouts.template')

@section('title')
    Cobrar
@endsection

@section('content')
    @include('cashier_counter.counter.modals.mdl_charge')
    <div class="card">

        <div class="card-header">
            <div class="row g-3 align-items-center">

                <!-- TÍTULO -->

                <div class="col-12">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-cash-register text-primary me-1"></i>
                        Cobrar Pedido
                    </h4>
                    <small class="text-muted">
                        <i class="fas fa-receipt text-secondary me-1"></i>
                        {{ $order->order_code }}
                    </small>
                </div>

                <!-- INFO TIPO POS -->
                <div class="col-12">
                    <div class="row g-2 text-center">

                        <!-- MESA -->
                        <div class="col-6 col-md-3">
                            <div class="h-100 rounded border p-2">
                                <div class="text-muted small">
                                    <i class="fas fa-utensils text-info me-1"></i>
                                    MESA
                                </div>
                                <div class="fw-bold fs-5">
                                    {{ $order->table_name }}
                                </div>
                            </div>
                        </div>

                        <!-- CLIENTE -->
                        <div class="col-6 col-md-3">
                            <div class="h-100 rounded border p-2">
                                <div class="text-muted small">
                                    <i class="fas fa-user text-primary me-1"></i>
                                    CLIENTE
                                </div>
                                <div class="fw-bold fs-6">
                                    {{ $order->customer_name }}
                                </div>
                            </div>
                        </div>

                        <!-- MESERO -->
                        <div class="col-6 col-md-3">
                            <div class="h-100 rounded border p-2">
                                <div class="text-muted small">
                                    <i class="fas fa-user-tie text-success me-1"></i>
                                    MESERO
                                </div>
                                <div class="fw-bold fs-6">
                                    {{ $order->creator_user_name }}
                                </div>
                            </div>
                        </div>

                        <!-- FECHA -->
                        <div class="col-6 col-md-3">
                            <div class="h-100 rounded border p-2">
                                <div class="text-muted small">
                                    <i class="fas fa-calendar-alt text-warning me-1"></i>
                                    FECHA
                                </div>
                                <div class="fw-bold fs-6">
                                    {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        @include('cashier_counter.counter.tables.tbl_detail')
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 d-flex justify-content-end">
                    <div class="table-responsive">
                        @include('cashier_counter.counter.tables.tbl_amounts')
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-12 d-flex justify-content-end">

                    <!-- BOTÓN VOLVER -->
                    <button type="button" class="btn btn-danger me-1"
                        onclick="redirect('tenant.mostrador_cajero.mostrador.index')">
                        <i class="fas fa-arrow-left"></i> Volver
                    </button>

                    <!-- BOTÓN REGISTRAR -->
                    <button class="btn btn-primary btn-charge-create" type="button">
                        <i class="fas fa-save"></i> Cobrar
                    </button>

                </div>

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
        window.app = {
            order: @json($order),
            lstDetail: @json($lst_detail),
            customerFormatted:@json($customer_formatted)
        };
    </script>
    @vite(['resources/js/cashier_counter/counter/charge/main.js'])
@endsection
