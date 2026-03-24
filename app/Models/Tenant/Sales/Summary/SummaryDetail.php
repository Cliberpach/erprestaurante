<?php

namespace App\Models\Tenant\Sales\Summary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SummaryDetail extends Model
{
    use HasFactory;
    protected $table = 'summaries_details';

    protected $guarded = [''];
}
