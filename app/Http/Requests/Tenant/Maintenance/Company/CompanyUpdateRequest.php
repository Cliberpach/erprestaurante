<?php

namespace App\Http\Requests\Tenant\Maintenance\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class CompanyUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'department' => str_pad($this->department, 2, '0', STR_PAD_LEFT),
            'province'   => str_pad($this->province, 4, '0', STR_PAD_LEFT),
            'district'   => str_pad($this->district, 6, '0', STR_PAD_LEFT),
        ]);
    }

    public function rules(): array
    {
        return [

            'ruc' => 'nullable|string|max:11',

            'business_name' => 'required|string|max:255',
            'abbreviated_business_name' => 'required|string|max:255',

            'fiscal_address' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:255',

            'phone' => 'nullable|string|max:255',
            'cellphone' => 'nullable|string|max:255',

            'email' => 'nullable|email|max:255',

            'facebook' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'web' => 'nullable|string|max:255',

            'igv' => ['required', 'numeric', 'regex:/^\d{1,2}(\.\d{1,2})?$/'],

            'invoicing_status' => 'nullable',

            'department' => 'required|exists:landlord.departments,id',
            'province'   => 'required|exists:landlord.provinces,id',
            'district'   => 'required|exists:landlord.districts,id',

            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',

            'logo' => 'nullable|image|mimes:jpg,jpeg,webp,avif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [

            'business_name.required' => 'El campo razón social es requerido',
            'business_name.max' => 'La razón social no debe superar los 255 caracteres',

            'abbreviated_business_name.required' => 'El campo razón social abreviada es requerido',
            'abbreviated_business_name.max' => 'La razón social abreviada no debe superar los 255 caracteres',

            'fiscal_address.max' => 'La dirección fiscal no debe superar los 255 caracteres',

            'email.email' => 'El email debe tener un formato válido',

            'igv.required' => 'El IGV es requerido',
            'igv.numeric' => 'El IGV debe ser numérico',
            'igv.regex' => 'El IGV debe tener máximo 2 enteros y 2 decimales',

            'department.required' => 'El departamento es requerido',
            'department.exists' => 'El departamento seleccionado no es válido',

            'province.required' => 'La provincia es requerida',
            'province.exists' => 'La provincia seleccionada no es válida',

            'district.required' => 'El distrito es requerido',
            'district.exists' => 'El distrito seleccionado no es válido',

            'logo.image' => 'El logo debe ser una imagen válida',
            'logo.mimes' => 'El logo debe ser jpg, jpeg, webp o avif',
            'logo.max' => 'El logo no debe superar los 2MB',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
