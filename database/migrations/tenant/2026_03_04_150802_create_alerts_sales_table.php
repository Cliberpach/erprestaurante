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
        Schema::create('alerts_sales', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('alert_id');
            $table->foreign('alert_id')->references('id')->on('alerts_app');

            $table->unsignedBigInteger('sale_id');
            $table->foreign('sale_id')->references('id')->on('sales');

            $table->string('sale_serie', 30);
            $table->decimal('matched_amount', 12, 2);

            $table->string('observation', 300)->nullable();
            $table->enum('status', ['USADO', 'ANULADO'])->default('USADO');

            $table->unsignedBigInteger('creator_user_id')->nullable();
            $table->string('creator_user_name', 255)->nullable();
            $table->unsignedBigInteger('editor_user_id')->nullable();
            $table->string('editor_user_name', 255)->nullable();
            $table->unsignedBigInteger('deletor_user_id')->nullable();
            $table->string('deletor_user_name', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts_sales');
    }
};
