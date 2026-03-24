<?php

namespace App\Http\Services\Tenant\Sale\Summary;


class SummaryManager
{
    protected SummaryService $s_service;

    public function __construct()
    {
        $this->s_service    =   new SummaryService();
    }

    public function store(array $data)
    {
        return $this->s_service->store($data);
    }

    public function consult($id)
    {
        return $this->s_service->consult($id);
    }
}
