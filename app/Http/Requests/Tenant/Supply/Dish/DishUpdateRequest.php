<?php

namespace App\Http\Requests\Tenant\Supply\Dish;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class DishUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $dishId = $this->route('id'); // o route('dish') según tu ruta

        return [

            // 🟢 Nombre del plato
            'name' => [
                'required',
                'string',
                'max:160',
                Rule::unique('dishes', 'name')
                    ->ignore($dishId)
                    ->where(function ($query) {
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
                'max:9999999999.999999',
            ],

            // 🟢 Precio de compra
            'purchase_price' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999999.999999',
            ],

            // 🟢 Imagen (opcional)
            'img' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,avif',
                'max:3072',
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
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
