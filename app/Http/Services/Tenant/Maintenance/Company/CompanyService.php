<?php

namespace App\Http\Services\Tenant\Maintenance\Company;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CompanyService
{

    public function __construct() {}

    public function startInvoicing(int $company_id,int $type_sale_id)
    {
        //====== ACTUALIZAR FACTURACIÓN A INICIADA PARA EL TYPE SALE RESPECTIVO ======
        DB::table('document_serializations')
            ->where('company_id', $company_id)
            ->where('document_type_id', $type_sale_id)
            ->where('initiated', 'NO')
            ->update([
                'initiated'     => 'SI',
                'updated_at'    => Carbon::now()
            ]);
    }
}
