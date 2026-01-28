<?php

namespace App\Http\Requests\Tenant\CashierCounter;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class InvoiceStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => [
                'required',
                Rule::exists('landlord.customers', 'id')
                    ->where('status', 'ACTIVO'),
            ],
            'invoice_id' => [
                'required',
                Rule::exists('landlord.general_table_details', 'id')
                    ->where('status', 'ACTIVO'),
            ],

        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'El cliente es obligatorio.',
            'customer_id.exists'   => 'El cliente seleccionado no existe o no está activo.',

            'invoice_id.required' => 'El cliente es obligatorio.',
            'invoice_id.exists'   => 'El cliente seleccionado no existe o no está activo.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
