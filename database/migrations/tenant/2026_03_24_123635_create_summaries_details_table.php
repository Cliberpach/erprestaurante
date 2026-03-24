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
        Schema::create('summaries_details', function (Blueprint $table) {
            $table->unsignedBigInteger('summary_id');
            $table->unsignedBigInteger('sale_id');

            $table->string('serie', 5)->nullable();
            $table->bigInteger('correlative')->nullable();
            $table->decimal('subtotal', 10, 2)->nullable();
            $table->decimal('igv', 10, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->string('customer_name', 200)->nullable();
            $table->string('customer_document_number', 20)->nullable();
            $table->string('customer_document_name', 200)->nullable();
            $table->decimal('percentage_igv', 15, 2)->nullable();

            $table->enum('status', ['ACTIVO', 'ANULADO'])->default('ACTIVO');

            $table->primary(['summary_id', 'sale_id']);
            $table->foreign('summary_id')->references('id')->on('summaries');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('summaries_details');
    }
};
