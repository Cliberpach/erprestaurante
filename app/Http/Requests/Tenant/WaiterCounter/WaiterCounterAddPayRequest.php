<?php

namespace App\Http\Requests\Tenant\WaiterCounter;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class WaiterCounterAddPayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method' => [
                'required',
                'integer',
                Rule::exists('payment_methods', 'id')
                    ->where(function ($query) {
                        $query->where('estado', 'ACTIVO');
                    }),
            ],

            'voucher' => [
                'required',
                'file',
                'image',
                'mimes:jpg,jpeg,png',
                'max:4096', // 4MB (Laravel usa KB)
            ],
        ];
    }

    public function messages(): array
    {
        return [
            // Payment method
            'payment_method.required' => 'El método de pago es obligatorio.',
            'payment_method.integer' => 'El método de pago seleccionado no es válido.',
            'payment_method.exists' => 'El método de pago seleccionado no existe o está inactivo.',

            // Voucher
            'voucher.required' => 'Debe subir el voucher de pago.',
            'voucher.file' => 'El voucher debe ser un archivo válido.',
            'voucher.image' => 'El voucher debe ser una imagen.',
            'voucher.mimes' => 'El voucher debe ser un archivo JPG o PNG.',
            'voucher.max' => 'El voucher no debe superar los 4MB.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
