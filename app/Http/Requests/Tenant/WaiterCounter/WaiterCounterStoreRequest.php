<?php

namespace App\Http\Requests\Tenant\WaiterCounter;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class WaiterCounterStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => [
                'required',
                Rule::exists('landlord.customers', 'id')
                    ->where('status', 'ACTIVO'),
            ],

            'payment_method' => [
                'nullable',
                Rule::exists('payment_methods', 'id')
                    ->where('estado', 'ACTIVO'),
            ],

            'observation' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.required' => 'El cliente es obligatorio.',
            'client_id.exists'   => 'El cliente seleccionado no existe o no está activo.',

            'payment_method.exists' => 'El método de pago seleccionado no existe o no está activo.',

            'observation.string' => 'La observación debe ser un texto válido.',
            'observation.max'    => 'La observación no debe superar los 500 caracteres.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
