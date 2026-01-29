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
        Schema::create('sales_products', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('sale_id');
            $table->foreign('sale_id')->references('id')->on('sales');

            $table->unsignedBigInteger('warehouse_id');
            $table->string('warehouse_name', 120);

            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->string('product_name', 160);

            $table->unsignedBigInteger('category_id');
            $table->string('category_name', 255);

            $table->unsignedBigInteger('brand_id');
            $table->string('brand_name', 255);

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
        Schema::dropIfExists('sales_products');
    }
};
