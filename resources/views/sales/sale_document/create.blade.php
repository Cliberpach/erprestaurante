@extends('layouts.template')

@section('title')
    Ventas
@endsection

@section('content')
    <x-card style="margin-top: 0;width:100%;">
        @csrf
        <x-slot name="headerCard">
            <h4 class="card-title">
                PUNTO DE VENTAS
            </h4>
        </x-slot>

        <x-slot name="contentCard">
            @include('sales.sale_document.forms.form_create')
        </x-slot>
    </x-card>
    @include('utils.modals.customer.mdl_create_customer')
@endsection

<style>
    /* Estado base: VISIBLE */
    .row-fade {
        overflow: hidden;
        max-height: 1000px;
        /* suficiente para el contenido */
        opacity: 1;
        transform: translateY(0);
        transition:
            max-height 0.5s ease,
            opacity 0.4s ease,
            transform 0.4s ease;
    }

    /* Estado oculto */
    .row-fade.hide {
        max-height: 0;
        opacity: 0;
        transform: translateY(-10px);
    }
</style>

<style>
    .swal2-container {
        z-index: 9999999;
    }

    .table-responsive {
        overflow-y: visible;
    }
</style>

@section('js')
    <script>
        window.app = {
            customerFormatted: @json($customer_formatted),
            companyIgv: @json($company->igv),
            paymentMethods: @json($payment_methods),
            init() {
                eventsMdlCreateCustomer();
            }
        };
    </script>
    @vite(['resources/js/sales/sales/main.js'])
@endsection
