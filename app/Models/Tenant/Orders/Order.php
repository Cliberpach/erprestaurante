<?php

namespace App\Models\Tenant\Orders;

use App\Http\Services\Tenant\Orders\OrderService;
use App\Models\Tenant\Sales\Sale\Sale;
use App\Models\Tenant\Supply\Table\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';

    protected $fillable = [
        'table_id',
        'customer_id',
        'customer_name',
        'customer_type_document_abbreviation',
        'customer_document_number',
        'date',
        'place',
        'observation',

        'n_attempts_dishes',
        'n_attempts_products',
        'status',
        'status_invoice',
        'pending_print',
        'pending_order_printing',
        'pending_kitchen_print',
        'kitchen_print_mode',
        'order_print_mode',

        'waiter_delete_status',
        'waiter_delete_name',
        'waiter_delete_user_id',
        'waiter_delete_at',
        'waiter_delete_observation',
        'cashier_delete_status',
        'cashier_delete_name',
        'cashier_delete_user_id',
        'cashier_delete_at',
        'creator_user_id',
        'creator_user_name',
        'editor_user_id',
        'editor_user_name',
        'delete_user_id',
        'delete_user_name',
        'code',

        'igv_percentage',
        'subtotal',
        'total',
        'igv',

        'payref_id',
        'payref_img_url',
        'payref_img_name',
        'payref_name',
        
        'payref_user_id',
        'payref_user_name',
        'payref_date',

        'sale_id',
        'sale_serie',
        'sale_correlative',

        'petty_cash_book_id',
        'petty_cash_id',
        'petty_cash_name',

        'date_pending_print',
        'date_pending_order_print',
        'date_change_table'
    ];

    protected $guarded = ['code'];

    public function table()
    {
        return $this->belongsTo(Table::class, 'table_id');
    }

    public function getDetails()
    {
        $service = new OrderService();
        return $service->getDetails($this->id);
    }

    public function sale()
    {
        return $this->hasOne(Sale::class, 'order_id', 'id');
    }

    public function hasSale(): bool
    {
        return $this->sale()->exists();
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

        static::created(function ($order) {
            $order->code = 'PED-' . str_pad($order->id, 8, '0', STR_PAD_LEFT);
            $order->saveQuietly();
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
