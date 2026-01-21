<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders_products', function (Blueprint $table) {

            /* 🔹 RELATIONS */
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('product_id');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('product_id')->references('id')->on('products');

            $table->string('product_name', 160);

            /* 🔹 DETAIL INFO */
            $table->decimal('sale_price', 10, 6);
            $table->integer('quantity');

            /* 🔹 STATUS */
            $table->string('status', 255)->default('PENDIENTE');
            $table->integer('delete_status')->default(1);

            /* 🔹 DESCRIPTION */
            $table->string('observation', 20)->nullable();

            /* 🔹 PRINT STATUS */
            $table->enum('print_status', ['IMPRESO', 'SIN_IMPRIMIR'])
                ->default('SIN_IMPRIMIR');

            $table->enum('print_delivery_status', ['CREADO', 'ENTREGADO'])
                ->default('CREADO');

            $table->string('detail_printed', 5)->nullable();

            $table->primary(['order_id', 'product_id'], 'pk_order_product');

            /* 🔹 TIMESTAMPS */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders_products');
    }
};
