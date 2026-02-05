<?php

namespace App\Http\Services\Tenant\CreditNote;

use App\Models\Tenant\Sales\CreditNote\CreditNote;
use Exception;

class CreditNoteValidation
{
    public function validationSend(CreditNote $instance)
    {
        if ($instance->sunat_status === 'ACEPTADO') {
            throw new Exception('NOTA CRÉDITO: ' . $instance->serie . '-' . $instance->correlative . ', YA FUE ACEPTADO POR SUNAT');
        }
        if ($instance->sunat_status === 'ENVIADO') {
            throw new Exception('NOTA CRÉDITO: ' . $instance->serie . '-' . $instance->correlative . ', YA FUE ENVIADO A SUNAT');
        }
    }
}
