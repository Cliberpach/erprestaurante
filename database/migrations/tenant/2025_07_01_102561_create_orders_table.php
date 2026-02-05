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

            //===== CODIGO =========
            $table->string('code', 20)->unique()->nullable();

            //======= MESA ==========
            $table->unsignedBigInteger('table_id');
            $table->foreign('table_id')->references('id')->on('tables');

            //======== CLIENTE
            $table->unsignedBigInteger('customer_id');
            $table->string('customer_name', 160);
            $table->string('customer_type_document_abbreviation', 20);
            $table->string('customer_document_number', 20);

            //========== CAJA =========
            $table->unsignedBigInteger('petty_cash_id');
            $table->foreign('petty_cash_id')->references('id')->on('petty_cashes');
            $table->string('petty_cash_name', 160);
            $table->unsignedBigInteger('petty_cash_book_id');
            $table->foreign('petty_cash_book_id')->references('id')->on('petty_cash_books');

            $table->date('date');
            $table->string('place')->default('LOCAL');
            $table->string('observation', 500)->nullable();
            $table->unsignedInteger('n_attempts_dishes')->nullable();
            $table->unsignedInteger('n_attempts_products')->nullable();

            $table->decimal('igv_percentage', 16, 6)->unsigned();
            $table->decimal('total', 16, 6)->unsigned();
            $table->decimal('subtotal', 16, 6)->unsigned();
            $table->decimal('igv', 16, 6)->unsigned();

            $table->enum('status', ['ACTIVO', 'FINALIZADO', 'ANULADO'])->default('ACTIVO');

            $table->unsignedBigInteger('payref_id')->nullable()->comment('ID de la referencia de pago (payref)');
            $table->foreign('payref_id')->references('id')->on('payment_methods');

            $table->string('payref_name', 160)->nullable()->comment('Nombre de la referencia de pago (payref)');
            $table->longText('payref_img_url')->nullable()->comment('Imagen de la referencia de pago (payref)');
            $table->longText('payref_img_name')->nullable()->comment('Nombre de la imagen de la referencia de pago (payref)');

            /* 🔹 PRINT CONFIGURATION */
            $table->enum('pending_print', ['SI', 'NO'])->default('SI');
            $table->enum('pending_order_printing', ['SI', 'NO'])->default('SI');
            $table->enum('order_print_mode', ['TODO', 'PARCIAL','PLATO','BEBIDA'])->default('TODO');

            /* 🔹 WAITER DELETE INFO */
            $table->boolean('waiter_delete_status')->default(0)->nullable();
            $table->string('waiter_delete_name', 200)->nullable();
            $table->unsignedInteger('waiter_delete_user_id')->nullable();
            $table->dateTime('waiter_delete_at')->nullable();
            $table->string('waiter_delete_observation', 300)->nullable();

            /*FACTURACION*/
            $table->enum('status_invoice', ['FACTURADO', 'NO FACTURADO'])->default('NO FACTURADO');
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->string('sale_serie', 100)->nullable();
            $table->unsignedInteger('sale_correlative')->nullable();

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
            $table->dateTime('date_pending_print')->nullable();
            $table->dateTime('date_pending_order_print')->nullable();

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
