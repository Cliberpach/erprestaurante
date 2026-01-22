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
        Schema::create('reservations', function (Blueprint $table) {

            /* 🔹 PRIMARY KEY */
            $table->id();

            $table->unsignedBigInteger('table_id');
            $table->foreign('table_id')->references('id')->on('tables');

            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders');

            /* 🔹 INDEXED FIELDS */
            $table->unsignedBigInteger('customer_id')->index();

            /* 🔹 DATA */
            $table->dateTime('date');
            $table->enum('status', ['OCUPADO', 'FINALIZADO', 'ANULADO'])->default('OCUPADO');
            $table->integer('estado_delete')->default(1);

            /* 🔹 AUDIT */
            $table->unsignedBigInteger('creator_user_id')->nullable();
            $table->string('creator_user_name')->nullable();
            $table->unsignedBigInteger('editor_user_id')->nullable();
            $table->string('editor_user_name')->nullable();
            $table->unsignedBigInteger('delete_user_id')->nullable();
            $table->string('delete_user_name')->nullable();


            /* 🔹 TIMESTAMPS */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
