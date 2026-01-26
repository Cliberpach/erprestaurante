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
            $table->id();

            /* 🔹 RELATIONS */
            $table->unsignedBigInteger('order_id')->index();
            $table->foreign('order_id')->references('id')->on('orders');

            $table->unsignedBigInteger('warehouse_id');
            $table->string('warehouse_name', 120);

            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->string('product_name', 160);

            $table->unsignedBigInteger('category_id');
            $table->string('category_name', 255);

            $table->unsignedBigInteger('brand_id');
            $table->string('brand_name', 255);


            /* 🔹 DETAIL INFO */
            $table->decimal('sale_price', 16, 6);
            $table->integer('quantity');

            /* 🔹 STATUS */
            $table->string('status', 255)->default('PENDIENTE');
            $table->boolean('delete_status')->default(false);

            /* 🔹 DESCRIPTION */
            $table->string('observation', 20)->nullable();

            /* 🔹 PRINT STATUS */
            $table->enum('print_status', ['IMPRESO', 'SIN_IMPRIMIR'])->default('SIN_IMPRIMIR');
            $table->enum('print_delivery_status', ['CREADO', 'ENTREGADO'])->default('CREADO');
            $table->enum('detail_printed', ['SI', 'NO'])->default('NO');

            /* 🔹 NUEVAS COLUMNAS */
            $table->decimal('purchase_price', 16, 6)->unsigned();
            $table->decimal('total', 16, 6);

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
