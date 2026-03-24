<?php

namespace App\Http\Services\Tenant\Sale\Summary;

use App\Models\Tenant\Sales\Summary\Summary;
use App\Models\Tenant\Sales\Summary\SummaryDetail;

class SummaryRepository
{
    public function store(array $dto): Summary
    {
        return Summary::create($dto);
    }

    public function storeDetail(array $dto)
    {
        SummaryDetail::insert($dto);
    }

    public function find(int $id): Summary
    {
        return Summary::findOrFail($id);
    }

    public function getDetail(int $id)
    {
        return SummaryDetail::where('summary_id', $id)->where('status', 'ACTIVO')->get();
    }

    public function saveSunatData(array $data, Summary $instance): Summary
    {
        $instance->summary_result_success = $data['summary_result_success'];
        $instance->summary_result_error = $data['summary_result_error'];
        $instance->summary_result_ticket = $data['summary_result_ticket'] ?? $instance->summary_result_ticket;
        $instance->summary_name = $data['summary_name'] ?? $instance->summary_name;
        $instance->response_error = $data['response_error'];

        $instance->status_result_code = $data['status_result_code'];
        $instance->status_result_success = $data['status_result_success'];
        $instance->status_result_error_code = $data['status_result_error_code'];
        $instance->status_result_error_message = $data['status_result_error_message'];

        $instance->sunat_status = $data['sunat_status'];

        $instance->cdr_response_id = $data['cdr_response_id'];
        $instance->cdr_response_code = $data['cdr_response_code'];
        $instance->cdr_response_description = $data['cdr_response_description'];
        $instance->cdr_response_notes = $data['cdr_response_notes'];
        $instance->cdr_response_reference = $data['cdr_response_reference'];

        $instance->route_cdr = $data['route_cdr'];
        $instance->route_xml = $data['route_xml'];

        $instance->send_sunat = $data['send_sunat'] ?? $instance->send_sunat;
        $instance->summary_result = $data['summary_result'] ?? $instance->summary_result;
        $instance->last_message =   $data['message'];

        $instance->save();

        return $instance;
    }
}
