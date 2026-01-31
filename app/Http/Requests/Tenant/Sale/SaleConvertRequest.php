<?php

namespace App\Http\Requests\Tenant\Sale;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class SaleConvertRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = $this->all();

        foreach ($data as $key => $value) {
            if (str_ends_with($key, '_mc')) {
                $data[str_replace('_mc', '', $key)] = $value;
                unset($data[$key]);
            }
        }

        $this->replace($data);
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
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'El cliente es obligatorio.',
            'customer_id.exists'   => 'El cliente seleccionado no existe o no está activo.',

            'invoice_id.required' => 'El tipo de comprobante es obligatorio.',
            'invoice_id.exists'   => 'El tipo de comprobante no existe o no está activo.',
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
