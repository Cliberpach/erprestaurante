<?php

namespace App\Models\Tenant\Supply\Table;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableFusion extends Model
{
    use HasFactory;

    protected $table = 'table_fusions';

    protected $fillable = [
        'order_id',
        'master_table_id',
        'slave_table_id',
        'status',
        'creator_user_id',
        'creator_user_name',
        'editor_user_id',
        'editor_user_name',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->creator_user_id   = auth()->id();
                $model->creator_user_name = auth()->user()->name;
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->editor_user_id   = auth()->id();
                $model->editor_user_name = auth()->user()->name;
            }
        });
    }

    public function masterTable()
    {
        return $this->belongsTo(Table::class, 'master_table_id');
    }

    public function slaveTable()
    {
        return $this->belongsTo(Table::class, 'slave_table_id');
    }
}
