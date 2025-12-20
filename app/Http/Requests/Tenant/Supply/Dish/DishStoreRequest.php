<?php

namespace App\Http\Requests\Tenant\Supply\Dish;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class DishStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            // 🟢 Nombre del plato
            'name' => [
                'required',
                'string',
                'max:160',
                Rule::unique('dishes')->where(function ($query) {
                    return $query->where('status', 'ACTIVO');
                }),
            ],

            // 🟢 Tipo de plato
            'type_dish_id' => [
                'required',
                'integer',
                Rule::exists('types_dish', 'id')->where(function ($query) {
                    return $query->where('status', 'ACTIVO');
                }),
            ],

            // 🟢 Precio de venta
            'sale_price' => [
                'required',
                'numeric',
                'min:0.000001',
                'max:9999999999.999999', // respeta DECIMAL(16,6)
            ],

            // 🟢 Precio de compra
            'purchase_price' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999999.999999',
            ],

            // 🟢 Imagen
            'img' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,avif',
                'max:3072', // 3MB
            ],
        ];
    }

    public function messages(): array
    {
        return [

            // name
            'name.required' => 'El nombre del plato es obligatorio.',
            'name.string'   => 'El nombre del plato debe ser texto.',
            'name.max'      => 'El nombre del plato no debe exceder los 160 caracteres.',
            'name.unique'   => 'Ya existe un plato con este nombre en estado ACTIVO.',

            // type_dish_id
            'type_dish_id.required' => 'Debe seleccionar un tipo de plato.',
            'type_dish_id.exists'   => 'El tipo de plato seleccionado no es válido o está ANULADO.',

            // sale_price
            'sale_price.required' => 'El precio de venta es obligatorio.',
            'sale_price.numeric'  => 'El precio de venta debe ser numérico.',
            'sale_price.min'      => 'El precio de venta debe ser mayor a 0.',

            // purchase_price
            'purchase_price.required' => 'El precio de compra es obligatorio.',
            'purchase_price.numeric'  => 'El precio de compra debe ser numérico.',
            'purchase_price.min'      => 'El precio de compra no puede ser negativo.',

            // img
            'img.mimes' => 'La imagen debe ser JPG, JPEG, PNG, WEBP o AVIF.',
            'img.max'   => 'La imagen no debe superar los 3MB.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException(
            $validator,
            response()->json([
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
