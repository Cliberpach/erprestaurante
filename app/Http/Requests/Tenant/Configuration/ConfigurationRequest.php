<?php

namespace App\Http\Requests\Tenant\Configuration;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class ConfigurationRequest extends FormRequest
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
            'configuration_password_2' => [
                'required_if:configuration_2,on',
                'string',
                'min:8',
                'max:30',
            ],
        ];
    }


    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'configuration_password_2.required_if' => 'La contraseña es obligatoria cuando la opción está activada.',
            'configuration_password_2.min'         => 'La contraseña debe tener al menos 8 caracteres.',
            'configuration_password_2.max'         => 'La contraseña no puede tener más de 30 caracteres.',
        ];
    }


    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
