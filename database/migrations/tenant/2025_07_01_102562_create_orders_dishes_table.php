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
        Schema::create('orders_dishes', function (Blueprint $table) {

            /* 🔹 RELATIONS */
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('dish_id');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('dish_id')->references('id')->on('dishes');

            $table->string('dish_name', 160);

            /* 🔹 DETAIL INFO */
            $table->decimal('sale_price', 16, 6);
            $table->integer('quantity');

            /* 🔹 STATUS */
            $table->string('status', 255)->default('PENDIENTE');
            $table->integer('delete_status')->default(1);

            /* 🔹 DESCRIPTION */
            $table->string('obseravation', 500)->nullable();

            /* 🔹 PRINT STATUS */
            $table->enum('print_status', ['IMPRESO', 'SIN_IMPRIMIR'])
                ->default('SIN_IMPRIMIR');

            $table->enum('print_delivery_status', ['CREADO', 'ENTREGADO'])
                ->default('CREADO');

            $table->string('detail_printed', 5)->nullable();

            $table->primary(['order_id', 'dish_id'], 'pk_order_dish');

            /* 🔹 TIMESTAMPS */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders_dishes');
    }
};
