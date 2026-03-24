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
        Schema::create('summaries', function (Blueprint $table) {
            $table->id();

            $table->string('serie', 10)->nullable();
            $table->unsignedBigInteger('correlative');

            $table->boolean('regularize')->default(0);
            $table->boolean('send_sunat')->default(0);

            $table->string('summary_result_success', 50)->nullable();
            $table->string('summary_result_error', 50)->nullable();
            $table->string('summary_result_ticket', 50)->nullable();

            $table->date('date_invoices')->nullable();

            $table->longText('route_xml')->nullable();
            $table->longText('route_cdr')->nullable();

            $table->longText('response_error')->nullable();
            $table->string('cdr_response_id', 100)->nullable();
            $table->string('cdr_response_code', 10)->nullable();
            $table->longText('cdr_response_description')->nullable();
            $table->string('summary_name', 100)->nullable();
            $table->longText('cdr_response_notes')->nullable();
            $table->string('status_result_code', 10)->nullable();
            $table->tinyInteger('status_result_success')->nullable();
            $table->longText('status_result_error_code')->nullable();
            $table->longText('status_result_error_message')->nullable();

            $table->unsignedBigInteger('creator_user_id')->nullable();
            $table->unsignedBigInteger('editor_user_id')->nullable();
            $table->unsignedBigInteger('deletor_user_id')->nullable();

            $table->string('deletor_user_name')->nullable();
            $table->string('editor_user_name')->nullable();
            $table->string('creator_user_name')->nullable();

            $table->enum('status', ['ACTIVO', 'ANULADO'])->default('ACTIVO');

            $table->enum('sunat_status', ['PENDIENTE', 'EN PROCESO', 'ACEPTADO', 'RECHAZADO'])
                ->default('PENDIENTE');

            $table->longText('cdr_response_reference')->nullable();
            $table->longText('summary_result')->nullable();
            $table->longText('last_message')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('summaries');
    }
};
