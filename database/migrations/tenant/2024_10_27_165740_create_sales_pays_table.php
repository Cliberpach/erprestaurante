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
        Schema::create('sales_pays', function (Blueprint $table) {

            $table->unsignedBigInteger('sale_id');
            $table->foreign('sale_id')->references('id')->on('sales');

            $table->unsignedBigInteger('payment_method_id');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
            $table->string('payment_method_name', 160);

            $table->decimal('amount', 16, 6);

            $table->enum('status', ['ACTIVO', 'ANULADO'])->default('ACTIVO');

            $table->primary(['sale_id', 'payment_method_id'], 'pk_sales_pays');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_pays');
    }
};
