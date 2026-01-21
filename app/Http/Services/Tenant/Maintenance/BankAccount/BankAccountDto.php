<?php

namespace App\Http\Services\Tenant\Maintenance\BankAccount;

use App\Models\Company;
use App\Models\Landlord\GeneralTable\GeneralTableDetail;

class BankAccountDto
{

    public function getDtoStore(array $data): array
    {
        $dto                        =   [];

        $bank                       =   GeneralTableDetail::findOrFail($data['bank_id']);

        $dto['bank_id']             =   $data['bank_id'];
        $dto['bank_name']           =   $bank->name;
        $dto['bank_abbreviation']   =   $bank->symbol;
        $dto['account_number']      =   $data['account_number'];
        $dto['cci']                 =   $data['cci'];
        $dto['phone']               =   $data['phone'];
        $dto['holder']              =   mb_strtoupper($data['holder'], 'UTF-8');
        $dto['currency']            =   mb_strtoupper($data['currency'], 'UTF-8');
        $dto['editable']            =   true;

        $qr                         =   $data['qr'] ?? null;
        $files_route                =   Company::findOrFail(1)->files_route;

        if ($qr) {
            $qr_name                =   uniqid() . '_' . trim($qr->getClientOriginalName());
            $dto['qr_url']          =   $files_route . '/bank_accounts/' . $qr_name;
            $dto['qr_name']         =   $qr_name;
        }

        return $dto;
    }

}
