<?php

namespace App\Http\Services\Tenant\Sale\Sale;

class CalculationsService
{

    public function __construct() {}

    /*
        RESPUESTA:
        {#2037 // app\Http\Controllers\Tenant\SaleController.php:138
            +"subtotal": 20.338983050847
            +"igv_amount": 3.6610169491525
            +"total": 24.0
        }
    */
    public static function calculateAmounts(array $lstItems, float $igv_percentage): object
    {
        $subtotal   =   0;
        $igv_amount =   0;
        $total      =   0;

        foreach ($lstItems as $item) {
            $total  +=  (float)$item->cant * (float)$item->sale_price;
        }
        $subtotal       =   $total / ((100 + (float)$igv_percentage) / 100);
        $igv_amount     =   $total - $subtotal;


        return (object)['subtotal' => $subtotal, 'igv_amount' => $igv_amount, 'total' => $total];
    }

    public function calculateCAmounts(array $data): object
    {
        $discount       =   $data['amounts']->discount;
        $igv_percentage =   $data['igv_percentage'];
        $factor         =   (100 + $igv_percentage) / 100;
      
        $discount_base  =   $discount / $factor;
        $discount_igv   =   $discount - $discount_base;

        return (object)[
            'discount'      =>  $discount,
            'discount_base' =>  $discount_base,
            'discount_igv'  =>  $discount_igv,
            'total_pay'     =>  $data['total_pay']
        ];
    }
}
