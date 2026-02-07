<?php

namespace App\Http\Requests\Tenant\Maintenance\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class CompanyInvoiceRequest extends FormRequest
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
            'urbanization' => [
                'required',
                'string',
                'max:160',
                'regex:/^[a-zA-Z0-9\s\-_.]+$/'
            ],

            'local_code' => [
                'nullable',
                'digits_between:1,4'
            ],

            'sol_user' => [
                'required',
                'string',
                'max:120'
            ],

            'sol_pass' => [
                'required',
                'string',
                'max:120'
            ],

            'api_user_gre' => [
                'nullable',
                'string',
                'max:120'
            ],

            'api_pass_gre' => [
                'nullable',
                'string',
                'max:120'
            ],

            'certificate' => [
                'nullable',
                'file',
                'mimetypes:application/x-pkcs12,application/pkcs12,application/octet-stream,text/plain',
            ],

            'certificate_password' => [
                'nullable',
                'string',
                'max:100',
                'required_if:certificate,p12'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'urbanization.required' => 'La urbanización es obligatoria.',
            'urbanization.max' => 'La urbanización no debe superar los 160 caracteres.',
            'urbanization.regex' => 'La urbanización solo puede contener letras, números y caracteres básicos.',

            'local_code.digits_between' => 'El código de local debe tener como máximo 4 dígitos numéricos.',

            'sol_user.required' => 'El usuario SOL es obligatorio.',
            'sol_user.max' => 'El usuario SOL no debe superar los 120 caracteres.',

            'sol_pass.required' => 'La contraseña SOL es obligatoria.',
            'sol_pass.max' => 'La contraseña SOL no debe superar los 120 caracteres.',

            'api_user_gre.max' => 'El usuario de la API Greenter no debe superar los 120 caracteres.',
            'api_pass_gre.max' => 'La contraseña de la API Greenter no debe superar los 120 caracteres.',

            'certificate.file' =>
            'El certificado debe ser un archivo válido.',

            'certificate.mimetypes' =>
            'El certificado debe estar en formato .pem o .p12.',

            'certificate_password.max' =>
            'La contraseña del certificado no debe superar los 100 caracteres.'
        ];
    }


    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
