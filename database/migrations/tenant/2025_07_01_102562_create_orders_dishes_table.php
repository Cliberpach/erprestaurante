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

            $table->id();

            /* 🔹 RELATIONS */
            $table->unsignedBigInteger('order_id')->index();
            $table->foreign('order_id')->references('id')->on('orders');

            $table->unsignedBigInteger('dish_id');
            $table->foreign('dish_id')->references('id')->on('dishes');
            $table->string('dish_name', 160);

            $table->unsignedBigInteger('type_dish_id');
            $table->string('type_dish_name', 160);

            $table->unsignedBigInteger('programming_id');

            /* 🔹 DETAIL INFO */
            $table->decimal('purchase_price', 16, 6)->unsigned();
            $table->decimal('sale_price', 16, 6);
            $table->integer('quantity');
            $table->decimal('total', 16, 6);

            /* 🔹 STATUS */
            $table->string('status', 255)->default('PENDIENTE');
            $table->boolean('delete_status')->default(false);
            $table->dateTime('cancellation_date')->nullable();

            /* 🔹 DESCRIPTION */
            $table->string('observation', 20)->nullable();

            /* 🔹 PRINT STATUS */
            $table->enum('print_status', ['IMPRESO', 'SIN_IMPRIMIR'])->default('SIN_IMPRIMIR');
            $table->enum('print_delivery_status', ['CREADO', 'ENTREGADO'])->default('CREADO');
            $table->enum('detail_printed', ['SI', 'NO'])->default('NO');


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
