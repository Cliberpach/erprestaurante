<?php

namespace App\Models\Tenant\Sales\CreditNote;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    use HasFactory;

    protected $table = 'credit_notes';
    protected $connection   =   'tenant';

    protected $fillable = [

        // 🔹 Documento afectado
        'sale_id',
        'petty_cash_id',
        'petty_cash_book_id',
        'petty_cash_name',

        'type_doc_affected',
        'num_doc_affected',
        'code_motive',
        'description_motive',
        'type_money',

        // 🔹 Almacén / tipo de comprobante
        'warehouse_id',
        'warehouse_name',
        'type_sale_id',
        'type_sale_code',
        'type_sale_name',

        // 🔹 Cliente
        'customer_id',
        'customer_name',
        'customer_type_document',
        'customer_document_number',
        'customer_document_code',
        'customer_phone',

        //====
        'type_invoice_id',
        'type_invoice_name',
        'type_invoice_code',

        // 🔹 Montos
        'igv_percentage',
        'subtotal',
        'igv_amount',
        'total',
        'legend',

        // 🔹 Totales SUNAT / UBL
        'mto_oper_taxed',
        'mto_igv',
        'total_taxes',
        'mto_imp_sale',
        'ubl_version',

        // 🔹 Serie y correlativo
        'correlative',
        'serie',

        // 🔹 Observaciones / estado
        'observation',
        'sunat_status',

        // 🔹 Respuesta SUNAT
        'response_cdrZip',
        'response_success',
        'response_error_code',
        'response_error_message',
        'cdr_response_id',
        'cdr_response_code',
        'cdr_response_description',
        'cdr_response_notes',
        'cdr_response_reference',
        'ruta_cdr',
        'ruta_xml',
        'ruta_qr',

        // 🔹 Auditoría
        'creator_user_id',
        'creator_user_name',
        'editor_user_id',
        'editor_user_name',
        'delete_user_id',
        'delete_user_name',

        'last_send_messages'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->creator_user_id = auth()->id();
                $model->creator_user_name = auth()->user()->name;
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->editor_user_id = auth()->id();
                $model->editor_user_name = auth()->user()->name;
            }
            if ($model->isDirty('status') && $model->status === 'ANULADO') {
                if (auth()->check()) {
                    $model->delete_user_id = auth()->id();
                    $model->delete_user_name = auth()->user()->name;
                }
            }
        });
    }
}
