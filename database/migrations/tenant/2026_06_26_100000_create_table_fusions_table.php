<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('table_fusions', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders');

            $table->unsignedBigInteger('master_table_id');
            $table->foreign('master_table_id')->references('id')->on('tables');

            $table->unsignedBigInteger('slave_table_id');
            $table->foreign('slave_table_id')->references('id')->on('tables');

            $table->enum('status', ['ACTIVO', 'FINALIZADO', 'ANULADO'])->default('ACTIVO');

            $table->unsignedBigInteger('creator_user_id')->nullable();
            $table->string('creator_user_name')->nullable();
            $table->unsignedBigInteger('editor_user_id')->nullable();
            $table->string('editor_user_name')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('table_fusions');
    }
};
