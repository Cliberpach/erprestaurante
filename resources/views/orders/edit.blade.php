@extends('layouts.template')

@section('title')
    Pedidos
@endsection

@push('js-head')
    @vite(['resources/js/libs/filepond.js'])
@endpush

@section('content')
    @include('orders.modals.mdl_dishes')
    @include('orders.modals.mdl_products')
    @include('orders.modals.mdl_edit_item')
    @include('orders.modals.mdl_delete_item')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h4 class="card-title mb-md-0 mb-2">EDITAR PEDIDO</h4>

            <div class="d-flex flex-wrap gap-2">

            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    @include('orders.forms.form_edit')
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
                    <button class="btn btn-primary" form="form_edit" type="submit">
                        <i class="fas fa-save"></i> ACTUALIZAR
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

    /*morado suave*/
    .row-new-item {
        background-color: rgba(108, 99, 255, 0.12) !important;
    }
</style>

@section('js')
    <script>
        window.app = {
            orderId: @json($order->id),
            customerFormatted: @json($customer_formatted),
            companyIgv: @json($igv),
            tableId: @json($table->id),
            lstDetail: @json($lst_detail),
            configDelete: @json($config_delete),
            init() {
                eventsMdlDishes();
                eventsMdlProductos();
                //eventsMdlEditItem();
                eventsMdlDeleteItem();
            }
        };
    </script>
    @vite(['resources/js/orders/edit/main.js'])
@endsection
