<?php

namespace App\Models\Tenant\Sales\Sale;

use App\Http\Services\Tenant\Sale\Sale\SaleService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Sale extends Model
{
    use HasFactory;

    protected $table = 'sales';
    protected $connection   =   'tenant';

    protected $fillable = [

        // =========================
        // WAREHOUSE
        // =========================
        'warehouse_id',
        'warehouse_name',

        // =========================
        // CUSTOMER
        // =========================
        'customer_id',
        'customer_name',
        'customer_type_document',
        'customer_document_number',
        'customer_document_code',
        'customer_phone',
        'customer_address',

        // =========================
        // PETTY CASH
        // =========================
        'petty_cash_id',
        'petty_cash_name',
        'petty_cash_book_id',

        // =========================
        // TYPE SALE
        // =========================
        'type_sale_id',
        'type_sale_code',
        'type_sale_name',

        // =========================
        // AMOUNTS
        // =========================
        'igv_percentage',
        'subtotal',
        'igv_amount',
        'total',
        'total_pay',
        'discount',
        'discount_base',
        'discount_igv',
        'change_pay',

        // SUNAT BASE CALC
        'mto_oper_gravadas',
        'mto_igv',
        'total_impuestos',
        'valor_venta',
        'sub_total',
        'mto_imp_venta',

        // =========================
        // DOCUMENT
        // =========================
        'legend',
        'correlative',
        'serie',

        // =========================
        // STATUS
        // =========================
        'status',
        'sunat_status',
        'pay_status',
        'pending_print',

        // =========================
        // SUNAT RESPONSE
        // =========================
        'response_cdrZip',
        'response_success',
        'response_error_code',
        'response_error_message',

        'cdr_response_id',
        'cdr_response_code',
        'cdr_response_description',
        'cdr_response_notes',
        'cdr_response_reference',

        // =========================
        // FILES
        // =========================
        'ruta_cdr',
        'ruta_xml',
        'ruta_qr',

        // =========================
        // PROCESS LOG
        // =========================
        'last_send_message',

        // =========================
        // TYPE / RELATIONS
        // =========================
        'type',
        'order_id',
        'public_hash',

        // =========================
        // PAYMENT CONDITION
        // =========================
        'payment_condition_id',
        'payment_condition_name',
        'payment_condition_days',
        'payment_status',

        // =========================
        // DATES
        // =========================
        'registration_date',
        'expiration_date',
        'date_pending_print',
        'send_at',
        'expires_at',
        'processing_at',
        'next_retry_at',

        // =========================
        // SUMMARY
        // =========================
        'summary_id',
        'summary_serie',

        // =========================
        // USERS (AUDIT)
        // =========================
        'creator_user_id',
        'editor_user_id',
        'deletor_user_id',

        'creator_user_name',
        'editor_user_name',
        'deletor_user_name',

        // =========================
        // RETRY / CONTROL
        // =========================
        'attempts',
    ];

    const STATUS_PENDING    = 'PENDIENTE';
    const STATUS_ACCEPTED   = 'ACEPTADO';
    const STATUS_FAILED     = 'RECHAZADO';
    const STATUS_OBSERVED   = 'OBSERVADO';
    const STATUS_EXPIRED    = 'EXPIRADO';

    protected $casts = [
        'expires_at' => 'datetime',
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

    public function calculateNextRetry(): Carbon
    {
        $minutes = match ($this->attempts) {
            1       => 15,
            2       => 60,
            3       => 180,
            4       => 480,
            default => 1440,
        };

        $nextRetry = now()->addMinutes($minutes);

        return $nextRetry->gt($this->expires_at)
            ? $this->expires_at->subMinutes(30)
            : $nextRetry;
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function markAsFailed(string $error, array $context = []): void
    {
        $this->increment('attempts');
        $this->refresh();

        $this->update([
            'last_error'    => ['message' => $error, 'context' => $context, 'at' => now()],
            'status'        => self::STATUS_PENDING,
            'next_retry_at' => $this->canRetry() ? $this->calculateNextRetry() : null,
        ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->creator_user_id = auth()->id();
                $model->creator_user_name = auth()->user()->name;
                $model->public_hash  = (string) Str::uuid();
                $model->expires_at = Carbon::now()->addDays(3);
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

        /*static::created(function (Sale $sale) {
            if (!in_array($sale->type_sale_code, ['01', '03'])) return;

            InvoiceDispatchLog::create([
                'tenant_id'        => Tenant::current()->id, // spatie multitenancy
                'invoiceable_id'   => $sale->id,
                'invoiceable_type' => Sale::class,
                'status'           => InvoiceDispatchLog::STATUS_PENDING,
                'expires_at'        => now()->addDays(3)->endOfDay(), // SUNAT: 3 días
            ]);
        });*/
    }
}
