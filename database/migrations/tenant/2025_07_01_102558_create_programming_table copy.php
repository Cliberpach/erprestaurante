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
        Schema::create('programming', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('petty_cash_book_id')->nullable();
            $table->foreign('petty_cash_book_id')->references('id')->on('petty_cash_books');

            $table->unsignedBigInteger('petty_cash_id')->nullable();
            $table->foreign('petty_cash_id')->references('id')->on('petty_cashes');

            $table->string('petty_cash_name', 255);

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');

            $table->decimal('quantity_dishes', 16, 6)->unsigned();
            $table->decimal('total', 16, 6)->unsigned();

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
        Schema::dropIfExists('programming');
    }
};
