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
        Schema::create('consumable_kardex', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('purchase_id')->nullable();
            $table->foreign('purchase_id')->references('id')->on('consumable_purchases');
            $table->string('purchase_code', 20)->nullable();

            $table->unsignedBigInteger('note_income_id')->nullable();
            $table->foreign('note_income_id')->references('id')->on('consumable_income_notes');
            $table->string('note_income_code', 20)->nullable();

            $table->unsignedBigInteger('note_release_id')->nullable();
            $table->foreign('note_release_id')->references('id')->on('notes_release');
            $table->string('note_release_code', 20)->nullable();

            $table->enum('type', ['ENTRADA', 'SALIDA']);
            $table->string('document_serie', 30);
            $table->dateTime('date');

            $table->unsignedBigInteger('warehouse_id');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->string('warehouse_name', 120);

            $table->unsignedBigInteger('consumable_id');
            $table->foreign('consumable_id')->references('id')->on('consumables');

            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('consumable_categories');

            $table->unsignedBigInteger('brand_id');
            $table->foreign('brand_id')->references('id')->on('consumable_brands');

            $table->string('unit_name', 160);
            $table->string('unit_symbol', 160);
            $table->string('consumable_name', 200);
            $table->string('category_name', 200);
            $table->string('brand_name', 200);

            $table->decimal('quantity', 16, 6)->unsigned();
            $table->decimal('sale_price', 16, 6)->unsigned();
            $table->decimal('purchase_price', 16, 6)->unsigned();
            $table->decimal('amount', 16, 6)->unsigned();

            $table->unsignedBigInteger('creator_user_id');
            $table->foreign('creator_user_id')->references('id')->on('users');
            $table->string('creator_user_name');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumable_kardex');
    }
};
