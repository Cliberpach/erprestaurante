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
        Schema::create('company_invoice', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('company_id')->unsigned()->nullable();
            $table->foreign('company_id')->references('id')->on('companies');

            $table->string('certificate')->nullable();
            $table->string('certificate_url')->nullable();
            $table->longText('certificate_password')->nullable();

            $table->string('secondary_user')->nullable();
            $table->string('secondary_password')->nullable();

            //======= API GUÍAS REMISIÓN ======
            $table->string('api_user_gre', 120)->nullable();
            $table->string('api_password_gre', 120)->nullable();

            $table->string('plan')->nullable();
            $table->string('environment')->nullable()->default('BETA');

            $table->longText('token_reniec')->nullable();

            $table->string('ubigeo', 20)->nullable();
            $table->unsignedBigInteger('department_id')->unsigned()->nullable();
            $table->foreign('department_id')->references('id')->on('departments');

            $table->unsignedBigInteger('province_id')->unsigned()->nullable();
            $table->foreign('province_id')->references('id')->on('provinces');

            $table->unsignedBigInteger('district_id')->unsigned()->nullable();
            $table->foreign('district_id')->references('id')->on('districts');

            $table->string('department_name', 300)->nullable();
            $table->string('province_name', 300)->nullable();
            $table->string('district_name', 300)->nullable();

            $table->enum('status', ['ACTIVO', 'ANULADO'])->default('ACTIVO');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_invoices');
    }
};
