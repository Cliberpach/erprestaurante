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
        Schema::create('consumable_income_note_details', function (Blueprint $table) {
            $table->unsignedBigInteger('consumable_income_note_id');
            $table->foreign('consumable_income_note_id')->references('id')->on('consumable_income_notes');

            $table->unsignedBigInteger('consumable_id');
            $table->foreign('consumable_id')->references('id')->on('consumables');

            $table->unsignedBigInteger('consumable_brand_id');
            $table->foreign('consumable_brand_id')->references('id')->on('consumable_brands');

            $table->unsignedBigInteger('consumable_category_id');
            $table->foreign('consumable_category_id')->references('id')->on('consumable_categories');

            $table->unsignedBigInteger('warehouse_id');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');

            $table->unsignedBigInteger('unit_id')->nullable();
            $table->string('unit_symbol', 160);
            $table->string('unit_name', 160);

            $table->string('warehouse_name', 160);
            $table->string('consumable_name', 160);
            $table->string('consumable_brand_name', 160);
            $table->string('consumable_category_name', 160);

            $table->decimal('quantity', 10, 2)->unsigned();

            $table->primary(
                ['consumable_income_note_id', 'consumable_id'],
                'pk_consumable_income_note_details'
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumable_income_note_details');
    }
};
