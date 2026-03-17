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
        Schema::create('consumable_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('status', ['ACTIVO', 'ANULADO'])->default('ACTIVO');
            $table->boolean('is_default')->default(false);

            $table->unsignedBigInteger('creator_user_id')->nullable();
            $table->foreign('creator_user_id')->references('id')->on('users');
            $table->string('creator_user_name');

            $table->unsignedBigInteger('editor_user_id')->nullable();
            $table->foreign('editor_user_name')->references('id')->on('users');
            $table->string('editor_user_name')->nullable();

            $table->unsignedBigInteger('deletor_user_id')->nullable();
            $table->foreign('deletor_user_id')->references('id')->on('users');
            $table->string('deletor_user_name')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumable_categories');
    }
};
