<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\Sales\PaymentCondition\PaymentCondition;
use Illuminate\Database\Seeder;


class PaymentConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $item                       =   new PaymentCondition();
        $item->name                 =   'CONTADO';
        $item->type                 =   'CONTADO';
        $item->nro_days             =   0;
        $item->editable             =   false;
        $item->save();

        $item                       =   new PaymentCondition();
        $item->name                 =   'CREDITO';
        $item->type                 =   'CREDITO';
        $item->nro_days             =   10;
        $item->editable             =   true;
        $item->save();

        $item                       =   new PaymentCondition();
        $item->name                 =   'CREDITO';
        $item->type                 =   'CREDITO';
        $item->nro_days             =   20;
        $item->editable             =   true;
        $item->save();
    }
}
