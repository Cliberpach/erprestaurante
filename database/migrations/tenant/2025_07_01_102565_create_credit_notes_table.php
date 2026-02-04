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
        Schema::create('credit_notes', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('sale_id');
            $table->foreign('sale_id')->references('id')->on('sales');

            $table->string('type_doc_affected', 160);
            $table->string('num_doc_affected', 160);
            $table->string('code_motive', 160);
            $table->string('description_motive', 160);
            $table->string('type_money')->default('PEN');

            $table->unsignedBigInteger('warehouse_id');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');

            $table->unsignedBigInteger('type_sale_id')->comment('DE LA VENTA AFECTADA');
            $table->string('type_sale_code', 160)->comment('DE LA VENTA AFECTADA');
            $table->string('type_sale_name', 160)->comment('DE LA VENTA AFECTADA');

            $table->unsignedBigInteger('type_invoice_id');
            $table->string('type_invoice_name', 160);
            $table->string('type_invoice_code', 160);

            $table->unsignedBigInteger('customer_id');
            $table->string('customer_name', 160);
            $table->enum('customer_type_document', ['DNI', 'RUC']);
            $table->string('customer_document_number', 20);
            $table->string('customer_document_code', 4);
            $table->string('customer_phone', 20)->nullable();

            //====== MONTOS =========
            $table->decimal('igv_percentage', 16, 6)->unsigned();
            $table->decimal('subtotal', 16, 6)->unsigned();
            $table->decimal('igv_amount', 16, 6)->unsigned();
            $table->decimal('total', 16, 6)->unsigned();
            $table->string('legend', 260);

            $table->decimal('mto_oper_taxed', 15, 2)->unsigned();
            $table->decimal('mto_igv', 15, 2)->unsigned();
            $table->decimal('total_taxes', 15, 2)->unsigned();
            $table->decimal('mto_imp_sale', 15, 2)->unsigned();
            $table->string('ubl_version')->default('2.1');

            //========= SERIE Y CORRELATIVO =======
            $table->unsignedInteger('correlative');
            $table->string('serie', 100);

            $table->string('observation', 200)->nullable();
            $table->enum('sunat_status', ['ACEPTADO', 'PENDIENTE', 'ENVIADO', 'RECHAZADO'])->default('PENDIENTE');

            //======= FACTURACIÓN ========
            $table->tinyInteger('response_cdrZip')->nullable();
            $table->tinyInteger('response_success')->nullable();
            $table->string('response_error_code', 255)->nullable();
            $table->string('response_error_message', 255)->nullable();
            $table->string('cdr_response_id', 255)->nullable();
            $table->string('cdr_response_code', 255)->nullable();
            $table->string('cdr_response_description', 255)->nullable();
            $table->longText('cdr_response_notes')->nullable();
            $table->longText('cdr_response_reference')->nullable();
            $table->longText('ruta_cdr')->nullable();
            $table->longText('ruta_xml')->nullable();
            $table->longText('ruta_qr')->nullable();

            /* 🔹 AUDIT */
            $table->unsignedBigInteger('creator_user_id')->nullable();
            $table->string('creator_user_name')->nullable();
            $table->unsignedBigInteger('editor_user_id')->nullable();
            $table->string('editor_user_name')->nullable();
            $table->unsignedBigInteger('delete_user_id')->nullable();
            $table->string('delete_user_name')->nullable();

            /* 🔹 TIMESTAMPS */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_notes');
    }
};
