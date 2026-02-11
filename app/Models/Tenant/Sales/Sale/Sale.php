<?php

namespace App\Models\Tenant\Sales\Sale;

use App\Http\Services\Tenant\Sale\Sale\SaleService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
        'customer_address',

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
        'discount',
        'change_pay',

        'legend',

        'correlative',
        'serie',

        'status',
        'sunat_status',
        'pay_status',
        'pending_print',

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
        'public_hash',

        'expiration_date',
        'registration_date',
        'payment_condition_id',
        'payment_condition_name',
        'payment_condition_days',
        'payment_status',

        'creator_user_id',
        'editor_user_id',
        'deletor_user_id',

        'deletor_user_name',
        'editor_user_name',
        'creator_user_name',
        'date_pending_print'
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

    public function getDetails()
    {
        $service = new SaleService();
        return $service->getDetails($this->id);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->creator_user_id = auth()->id();
                $model->creator_user_name = auth()->user()->name;
                $model->public_hash  = (string) Str::uuid();
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->editor_user_id = auth()->id();
                $model->editor_user_name = auth()->user()->name;
            }
            if ($model->isDirty('status') && $model->status === 'ANULADO') {
                if (auth()->check()) {
                    $model->deletor_user_id = auth()->id();
                    $model->deletor_user_name = auth()->user()->name;
                }
            }
        });
    }
}
