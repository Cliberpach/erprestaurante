<?php

namespace App\Http\Services\Tenant\CreditNote;

use App\Http\Services\Tenant\CreditNote\CreditNoteService;
use App\Models\Tenant\Sales\CreditNote\CreditNote;
use App\Models\Tenant\Sales\Sale\Sale;

class CreditNoteManager
{
    private CreditNoteService $s_service;

    public function __construct()
    {
        $this->s_service    =   new CreditNoteService();
    }

    public function sendSunat(int $credit_note_id): CreditNote
    {
        return $this->s_service->sendSunat($credit_note_id);
    }

    public function pdfOne(int $id)
    {
        return $this->s_service->pdfOne($id);
    }
}
