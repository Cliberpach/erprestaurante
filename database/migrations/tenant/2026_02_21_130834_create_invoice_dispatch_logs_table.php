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
        Schema::create('invoice_dispatch_logs', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');          // a qué tenant pertenece
            $table->morphs('invoiceable');         // polimórfico: boleta o factura
            $table->string('status')->default('PENDIENTE'); // pending|processing|sent|failed|expired
            $table->integer('attempts')->default(0);      // intentos realizados
            $table->integer('max_attempts')->default(5);  // máximo de intentos
            $table->dateTime('next_retry_at')->nullable(); // cuándo reintenta
            $table->dateTime('expires_at');               // +3 días desde emisión (SUNAT)
            $table->dateTime('sent_at')->nullable();       // cuándo se envió ok
            $table->json('last_error')->nullable();         // último error guardado
            $table->json('metadata')->nullable();           // respuesta SUNAT, extras
            $table->timestamps();

            $table->index(['tenant_id', 'status', 'next_retry_at']);
            $table->index(['expires_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_dispatch_logs');
    }
};
