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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('tenant_id')->unsigned()->nullable();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            
            $table->string('ruc')->unique();
            $table->string('business_name')->unique();
            $table->string('abbreviated_business_name')->unique()->nullable();

            $table->string('files_route', 200)->nullable();

            $table->string('logo_url')->nullable();
            $table->string('logo')->nullable();
            $table->longText('base64_logo')->nullable();

            $table->string('fiscal_address')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('phone')->nullable();
            $table->string('cellphone')->nullable();
            $table->string('email')->nullable();

            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('web')->nullable();

            // Facturacion Electronica
            $table->enum('invoicing_status', [0, 1])->default(0);
            $table->enum('status', [0, 1])->nullable()->default(1);

            // Plan
            $table->enum('plan', [1, 2, 3]);

            $table->longText('token_placa')->nullable();

            $table->unsignedBigInteger('department_id')->unsigned()->nullable();
            $table->foreign('department_id')->references('id')->on('departments');

            $table->unsignedBigInteger('province_id')->unsigned()->nullable();
            $table->foreign('province_id')->references('id')->on('provinces');

            $table->unsignedBigInteger('district_id')->unsigned()->nullable();
            $table->foreign('district_id')->references('id')->on('districts');

            $table->string('department_name', 300)->nullable();
            $table->string('province_name', 300)->nullable();
            $table->string('district_name', 300)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
