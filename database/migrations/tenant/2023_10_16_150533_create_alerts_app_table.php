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
        Schema::create('alerts_app', function (Blueprint $table) {
            $table->id();

            // =========================
            // TENANT
            // =========================
            $table->string('tenant_domain');

            // =========================
            // CONTENT
            // =========================
            $table->text('content');

            // =========================
            // DATES
            // =========================
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            // =========================
            // STATUS / TYPE
            // =========================
            $table->enum('status', ['PENDIENTE', 'USADO', 'ANULADO'])
                ->default('PENDIENTE')
                ->nullable();

            $table->enum('type', ['PAGO', 'STOCK'])
                ->default('PAGO')
                ->nullable();

            // =========================
            // CREATOR
            // =========================
            $table->unsignedBigInteger('creator_user_id')->nullable();
            $table->string('creator_user_name')->nullable();

            // =========================
            // CONSUMER (USER WHO USED IT)
            // =========================
            $table->unsignedBigInteger('consumer_user_id')->nullable();
            $table->string('consumer_user_name')->nullable();
            $table->dateTime('consumer_date')->nullable();

            // =========================
            // DELETOR (SOFT TRACKING)
            // =========================
            $table->unsignedBigInteger('deletor_user_id')->nullable();
            $table->string('deletor_user_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts_app');
    }
};
