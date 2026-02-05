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
        Schema::create('credit_notes_dishes', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('credit_note_id');
            $table->foreign('credit_note_id')->references('id')->on('credit_notes');

            $table->unsignedBigInteger('dish_id');
            $table->foreign('dish_id')->references('id')->on('dishes');
            $table->string('dish_name', 160);

            $table->unsignedBigInteger('type_dish_id');
            $table->string('type_dish_name', 160);

            $table->unsignedBigInteger('programming_id');
            $table->decimal('purchase_price', 16, 6)->unsigned();
            $table->decimal('sale_price', 16, 6);
            $table->integer('quantity');
            $table->decimal('total', 16, 6);

            $table->string('observation', 20)->nullable();

            // ===== SUNAT =====
            $table->decimal('mto_valor_unitario', 16, 6);
            $table->decimal('mto_valor_venta', 16, 6);
            $table->decimal('mto_base_igv', 16, 6);
            $table->decimal('porcentaje_igv', 16, 6);
            $table->decimal('igv', 16, 6);
            $table->unsignedBigInteger('tip_afe_igv');
            $table->decimal('total_impuestos', 16, 6);
            $table->decimal('mto_precio_unitario', 16, 6);

            $table->enum('status', ['ACTIVO', 'ANULADO'])->default('ACTIVO');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_notes_dishes');
    }
};
