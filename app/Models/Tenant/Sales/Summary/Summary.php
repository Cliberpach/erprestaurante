<?php

namespace App\Models\Tenant\Sales\Summary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Summary extends Model
{
    use HasFactory;
    protected $table = 'summaries';

    protected $fillable = [
        'serie',
        'correlative',
        'regularize',
        'summary_result_success',
        'summary_result_error',
        'summary_result_ticket',
        'date_invoices',
        'route_xml',
        'route_cdr',
        'response_error',
        'cdr_response_id',
        'cdr_response_code',
        'cdr_response_description',
        'summary_name',
        'cdr_response_notes',
        'status_result_code',
        'status_result_success',
        'status_result_error_code',
        'status_result_error_message',
        'sunat_status',
        'send_sunat',
        'last_message'
    ];
}
