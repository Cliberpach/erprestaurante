<?php

namespace App\Http\Requests\Company;

use App\Models\DocumentType;
use App\Models\Landlord\GeneralTable\GeneralTableDetail;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class CompanyNumerationRequest extends FormRequest
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
        return [
            'billing_type_document' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!GeneralTableDetail::where('id', $value)->where('status', 'ACTIVO')->exists()) {
                        $fail("El $attribute seleccionado no es válido.");
                    }
                },
            ],
            'start_number'  => 'required|integer|min:1|max:99999999',
            'serie' => [
                'required',
                'string',
                'size:4',
                function ($attribute, $value, $fail) {

                    $billingTypeId = $this->billing_type_document;

                    if (!$billingTypeId) {
                        $fail('El tipo de documento seleccionado no es válido.');
                        return;
                    }

                    $billingType = GeneralTableDetail::where('id', $billingTypeId)
                        ->where('status', 'ACTIVO')
                        ->first();

                    if (!$billingType) {
                        $fail('El tipo de documento seleccionado no es válido.');
                        return;
                    }

                    $length =   strlen($billingType->parameter);

                    $seriePrefix = substr($value, 0, $length);
                    if ($seriePrefix !== $billingType->parameter) {
                        $fail('Los dos primeros caracteres de la serie deben coincidir con el tipo de documento.');
                    }

                    $serieSufix = substr($value, $length);
                    if (!ctype_digit($serieSufix)) {
                        $fail('La serie debe contener el parámetro al inicio y luego números.');
                    }

                },
            ],
        ];
    }

    /**
     * Custom messages for validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'billing_type_document.required'    => 'El tipo de documento de facturación es obligatorio.',
            'billing_type_document.integer'     => 'El tipo de documento de facturación debe ser un número entero.',
            'billing_type_document.in'          => 'El tipo de documento de facturación debe ser uno de los siguientes valores: 1, 3, 6, 7, 9, 80.',

            'serie.required'                    => 'La serie es obligatoria.',

            'start_number.required'             => 'El número de inicio es obligatorio.',
            'start_number.integer'              => 'El número de inicio debe ser un número entero.',
            'start_number.min'                  => 'El número de inicio debe ser al menos 1.',
            'start_number.max'                  => 'El número de inicio no debe tener más de 8 dígitos.',
        ];
    }


    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
