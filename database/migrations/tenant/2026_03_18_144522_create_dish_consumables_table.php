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
        Schema::create('dish_consumables', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('dish_id');
            $table->unsignedBigInteger('consumable_id');
            $table->foreign('dish_id')->references('id')->on('dishes');
            $table->foreign('consumable_id')->references('id')->on('consumables');

            $table->decimal('quantity', 10, 2);

            $table->timestamps();

            $table->unique(['dish_id', 'consumable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dish_consumables');
    }
};
