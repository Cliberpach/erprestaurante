<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDocument extends Model
{
    use HasFactory;
    protected $table = 'purchase_documents';

    protected $fillable = [
        'warehouse_id',
        'warehouse_name',
        'delivery_date',
        'supplier_id',
        'supplier_name',
        'supplier_type_document_abbreviation',
        'supplier_document_number',
        'condition',
        'currency',
        'document_type',
        'serie',
        'correlative',
        'observation',
        'prices_with_igv',
        'igv',
        'subtotal',
        'amount_igv',
        'total',
        'status',

        'payment_condition_id',
        'payment_condition_name',
        'payment_condition_days',
        'payment_status',
        'registration_date',
        'expiration_date',

        'creator_user_id',
        'editor_user_id',
        'delete_user_id',

        'delete_user_name',
        'editor_user_name',
        'creator_user_name',
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
