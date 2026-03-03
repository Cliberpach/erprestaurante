@extends('layouts.template')

@section('title')
    Pedidos
@endsection

@push('js-head')
    @vite(['resources/js/libs/filepond.js'])
@endpush

@section('content')
    @include('utils.modals.customer.mdl_create_customer')
    @include('orders.modals.mdl_dishes')
    @include('orders.modals.mdl_products')
    @include('orders.modals.mdl_edit_item')
    @include('orders.modals.mdl_qr_payment')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h4 class="card-title mb-md-0 mb-2">REGISTRAR PEDIDO</h4>

            <div class="d-flex flex-wrap gap-2">

            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    @include('orders.forms.form_create')
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-12 d-flex justify-content-end">

                    <!-- BOTÓN VOLVER -->
                    <button type="button" class="btn btn-danger me-1"
                        onclick="redirect('tenant.mostrador_mesero.mostrador.index')">
                        <i class="fas fa-arrow-left"></i> VOLVER
                    </button>

                    <!-- BOTÓN REGISTRAR -->
                    <button class="btn btn-primary" form="form_create" type="submit">
                        <i class="fas fa-save"></i> REGISTRAR
                    </button>

                </div>

            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        window.app = {
            customerFormatted: @json($customer_formatted),
            companyIgv: @json($igv),
            tableId: @json($table->id),
            init() {
                eventsMdlDishes();
                eventsMdlProductos();
                eventsMdlEditItem();
                eventsMdlCreateCustomer();
                eventsMdlQrPay();
            }
        };
    </script>
    @vite(['resources/js/orders/create/main.js'])
@endsection
