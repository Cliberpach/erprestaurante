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
        Schema::create('consumable_purchase_details', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_id');
            $table->foreign('purchase_id')->references('id')->on('consumable_purchases');

            $table->unsignedBigInteger('consumable_id');
            $table->foreign('consumable_id')->references('id')->on('consumables');

            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('consumable_categories');

            $table->unsignedBigInteger('brand_id');
            $table->foreign('brand_id')->references('id')->on('consumable_brands');

            $table->unsignedBigInteger('warehouse_id');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');

            $table->string('warehouse_name', 160);
            $table->string('consumable_name', 200);
            $table->string('category_name', 200);
            $table->string('brand_name', 200);
            $table->string('unit_name', 160);
            $table->string('unit_symbol', 160);

            $table->decimal('quantity', 10, 2)->unsigned();
            $table->decimal('purchase_price', 10, 2)->unsigned();
            $table->decimal('subtotal', 10, 2)->unsigned();

            $table->primary(['purchase_id', 'consumable_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumable_purchase_details');
    }
};
