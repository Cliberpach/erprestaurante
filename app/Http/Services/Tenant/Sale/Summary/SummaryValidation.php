<?php

namespace App\Http\Services\Tenant\Sale\Summary;

use App\Models\Tenant\Sales\Summary\Summary;
use Exception;

class SummaryValidation
{
    public function validationSend(Summary $instance)
    {
        if ($instance->sunat_status === 'ACEPTADO') {
            throw new Exception('RESUMEN: ' . $instance->serie . '-' . $instance->correlative . ', YA FUE ACEPTADO POR SUNAT');
        }
        if ($instance->sunat_status === 'EN PROCESO') {
            throw new Exception('RESUMEN: ' . $instance->serie . '-' . $instance->correlative . ', YA FUE ENVIADO A SUNAT, SE ENCUENTRA EN PROCESO');
        }
    }
}
