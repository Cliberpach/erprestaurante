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
        Schema::create('consumables', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('consumable_categories');

            $table->unsignedBigInteger('brand_id');
            $table->foreign('brand_id')->references('id')->on('consumable_brands');

            $table->string('name', 500);
            $table->string('description', 255)->nullable();
            $table->decimal('sale_price', 10, 2);
            $table->decimal('purchase_price', 10, 2);
            $table->integer('stock');
            $table->integer('stock_min');
            $table->string('code_factory')->nullable();
            $table->string('code_bar')->nullable();
            $table->string('image')->nullable();
            $table->longText('img_route')->nullable();
            $table->longText('img_name')->nullable();

            $table->unsignedBigInteger('unit_id')->nullable();
            $table->string('unit_symbol', 160);
            $table->string('unit_name', 160);

            $table->enum('status', ['ACTIVO', 'ANULADO'])->default('ACTIVO');

            $table->unsignedBigInteger('creator_user_id');
            $table->foreign('creator_user_id')->references('id')->on('users');

            $table->unsignedBigInteger('editor_user_id')->nullable();
            $table->foreign('editor_user_id')->references('id')->on('users');

            $table->unsignedBigInteger('deletor_user_id')->nullable();
            $table->foreign('deletor_user_id')->references('id')->on('users');

            $table->string('deletor_user_name')->nullable();
            $table->string('editor_user_name')->nullable();
            $table->string('creator_user_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumables');
    }
};
