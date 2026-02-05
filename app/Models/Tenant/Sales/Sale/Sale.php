<?php

namespace App\Models\Tenant\Sales\Sale;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Sale extends Model
{
    use HasFactory;

    protected $table = 'sales';
    protected $connection   =   'tenant';

    protected $fillable = [

        'warehouse_id',
        'warehouse_name',

        'customer_id',
        'customer_name',
        'customer_type_document',
        'customer_document_number',
        'customer_document_code',
        'customer_phone',

        'petty_cash_id',
        'petty_cash_name',
        'petty_cash_book_id',

        'type_sale_id',
        'type_sale_code',
        'type_sale_name',

        'igv_percentage',
        'subtotal',
        'igv_amount',
        'total',
        'change_pay',

        'legend',

        'method_pay_id_1',
        'amount_pay_1',
        'method_pay_id_2',
        'amount_pay_2',

        'correlative',
        'serie',

        'status',
        'sunat_status',
        'pay_status',

        'response_cdrZip',
        'response_success',
        'response_error_code',
        'response_error_message',

        'cdr_response_id',
        'cdr_response_code',
        'cdr_response_description',
        'cdr_response_notes',
        'cdr_response_reference',

        'last_send_message',

        'ruta_cdr',
        'ruta_xml',
        'ruta_qr',

        'type',
        'order_id',

        'creator_user_id',
        'editor_user_id',
        'deletor_user_id',

        'deletor_user_name',
        'editor_user_name',
        'creator_user_name',
    ];


    public function pays()
    {
        return $this->hasMany(SalePay::class)
            ->select(
                'sale_id',
                'payment_method_id',
                DB::raw('SUM(amount) as total')
            )
            ->where('status', 'ACTIVO')
            ->groupBy('sale_id', 'payment_method_id');
    }

    public function paidByMethod(int $paymentMethodId): float
    {
        return $this->pays
            ->where('payment_method_id', $paymentMethodId)
            ->sum('total');
    }

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
