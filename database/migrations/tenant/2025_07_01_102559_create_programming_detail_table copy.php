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
        Schema::create('programming_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('programming_id')->nullable();
            $table->foreign('programming_id')->references('id')->on('programming');

            $table->unsignedBigInteger('dish_id')->nullable();
            $table->foreign('dish_id')->references('id')->on('dishes');

            $table->string('dish_name', 160);
            $table->string('type_dish_name', 160);

            $table->decimal('quantity', 16, 6)->unsigned();
            $table->decimal('purchase_price', 16, 6)->unsigned();
            $table->decimal('sale_price', 16, 6)->unsigned();

            $table->enum('status', ['ACTIVO', 'ANULADO'])->default('ACTIVO');

            $table->timestamps();

            $table->primary(['programming_id', 'dish_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programming_detail');
    }
};
