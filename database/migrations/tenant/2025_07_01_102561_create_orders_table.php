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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('customer_id');
            $table->string('customer_name', 160);
            $table->string('customer_type_document_abbreviation', 20);
            $table->string('customer_document_number', 20);

            $table->date('date');
            $table->string('place')->default('LOCAL');
            $table->string('observation', 500);
            $table->unsignedInteger('n_attempts_dishes');
            $table->unsignedInteger('n_attempts_products');

            $table->decimal('total', 16, 6)->unsigned();
            $table->decimal('subtotal', 16, 6)->unsigned();
            $table->decimal('igv', 16, 6)->unsigned();

            $table->enum('status', ['ACTIVO', 'FINALIZADO', 'ANULADO'])->default('ACTIVO');
            $table->enum('status_invoice', ['FACTURADO', 'NO FACTURADO'])->default('NO FACTURADO');

            /* 🔹 PRINT CONFIGURATION */
            $table->string('pending_print', 2)->default('NO')->nullable();
            $table->string('pending_kitchen_print', 5)->default('NO')->nullable();
            $table->string('kitchen_print_mode', 10)->default('TODO')->nullable();

            /* 🔹 WAITER DELETE INFO */
            $table->boolean('waiter_delete_status')->default(0)->nullable();
            $table->string('waiter_delete_name', 200)->nullable();
            $table->unsignedInteger('waiter_delete_user_id')->nullable();
            $table->dateTime('waiter_delete_at')->nullable();
            $table->string('waiter_delete_observation', 300)->nullable();

            /* 🔹 CASHIER DELETE INFO */
            $table->boolean('cashier_delete_status')->default(0)->nullable();
            $table->string('cashier_delete_name', 200)->nullable();
            $table->unsignedInteger('cashier_delete_user_id')->nullable();
            $table->dateTime('cashier_delete_at')->nullable();

            /* 🔹 AUDIT */
            $table->unsignedBigInteger('creator_user_id')->nullable();
            $table->string('creator_user_name')->nullable();
            $table->unsignedBigInteger('editor_user_id')->nullable();
            $table->string('editor_user_name')->nullable();
            $table->unsignedBigInteger('delete_user_id')->nullable();
            $table->string('delete_user_name')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
