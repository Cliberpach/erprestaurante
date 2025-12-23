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
        Schema::create('petty_cash_servers', function (Blueprint $table) {

            $table->unsignedBigInteger('petty_cash_book_id');
            $table->foreign('petty_cash_book_id')->references('id')->on('petty_cash_books');

            $table->unsignedBigInteger('user_id')->nullable()->comment('Mesero');
            $table->foreign('user_id')->references('id')->on('users');

            $table->timestamps();

            $table->primary(['petty_cash_book_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('petty_cash_servers');
    }
};
