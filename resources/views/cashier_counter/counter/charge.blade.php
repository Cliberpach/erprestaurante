@extends('layouts.template')

@section('title')
    Cobrar
@endsection

@section('content')
    @include('cashier_counter.counter.modals.mdl_charge')
    @include('utils.modals.customer.mdl_create_customer')
    <div class="card">

        <div class="card-header">
            <div class="row g-3 align-items-center mb-3">

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
            <div class="row align-items-center">
                <span class="legend-canceled me-2"></span>
                <small class="text-muted fw-semibold">
                    <i class="fas fa-square text-danger me-1"></i>
                    ITEMS ELIMINADOS
                </small>
            </div>
        </div>



        <div class="card-body">
            <div class="row mb-3">
                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                    <label for="filter_status">
                        <i class="fas fa-signal text-primary me-1"></i>
                        Estado Item
                    </label>
                    <select name="filter_status" id="filter_status">
                        <option value="TODO">Todo</option>
                        <option value="ACTIVO">Activo</option>
                        <option value="ELIMINADO">Eliminado</option>
                    </select>
                </div>
            </div>
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

@section('css')
    <style>
        .swal2-container {
            z-index: 9999999;
        }

        .item-canceled {
            background-color: #ed7272 !important;
            color: #4a0d12 !important;
            border-left: 4px solid #8f1822 !important;
        }

        .item-canceled td {
            background-color: transparent !important;
        }

        .item-canceled:hover {
            background-color: #c86a6a !important;
            transition: background-color 0.2s ease !important;
        }
    </style>
@endsection

@section('js')
    <script>
        window.app = {
            order: @json($order),
            lstDetail: @json($lst_detail),
            lstDetailCanceled: @json($lst_canceled),
            customerFormatted: @json($customer_formatted),
            eventsAdd: function() {
                eventsMdlCreateCustomer();
            }
        };
    </script>
    @vite(['resources/js/cashier_counter/counter/charge/main.js'])
@endsection
