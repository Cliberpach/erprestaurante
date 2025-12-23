<?php

namespace App\Models\Tenant\Cash;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PettyCashServer extends Model
{
    use HasFactory;
    protected $table = 'petty_cash_servers';

    protected $fillable = [
        'petty_cash_book_id',
        'user_id',
    ];
}
