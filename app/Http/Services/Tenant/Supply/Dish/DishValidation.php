<?php

namespace App\Http\Services\Tenant\Supply\Dish;

class DishValidation
{
    public function validationStore(array $data): array
    {
        $lst_sheet = json_decode($data['lstSheet']);

        $filtered = array_filter($lst_sheet, function ($item) {
            return isset($item->quantity)
                && is_numeric($item->quantity)
                && $item->quantity > 0;
        });

        $filtered = array_values($filtered);
        $data['lst_sheet']  =   $filtered;
        return $data;
    }
}
