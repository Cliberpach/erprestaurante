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
        Schema::create('dishes', function (Blueprint $table) {

            $table->id();

            $table->string('name', 160);

            $table->unsignedBigInteger('type_dish_id')->nullable();
            $table->foreign('type_dish_id')->references('id')->on('types_dish');

            $table->unsignedDecimal('sale_price', 16, 6)->nullable();
            $table->unsignedDecimal('purchase_price', 16, 6)->nullable();

            $table->longText('img_route')->nullable();
            $table->longText('img_name')->nullable();

            $table->enum('status', ['ACTIVO', 'ANULADO'])->default('ACTIVO');

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
        Schema::dropIfExists('dishes');
    }
};
