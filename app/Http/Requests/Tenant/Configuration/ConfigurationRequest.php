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
            'configuration_2' => ['nullable'],
            'configuration_3' => ['nullable'],

            'configuration_4' => [
                'required',
                'string',
                'min:8',
                'max:30'
            ]
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
            'configuration_4.required' => 'La contraseña es obligatoria.',
            'configuration_4.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'configuration_4.max' => 'La contraseña debe tener como máximo 30 caracteres.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
