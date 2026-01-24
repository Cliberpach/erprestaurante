<?php

namespace App\Http\Requests\Landlord\Api\AlertApp;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;

class AlertAppRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación.
     */
    public function rules(): array
    {
        return [
            'tenant_domain' => ['required', 'string', 'max:191'],
            'user_id'       => ['nullable', 'integer'],
            'content'       => ['required', 'string', 'max:65535'],
            'sent_at'       => ['nullable', 'date'],
        ];
    }

    /**
     * Mensajes personalizados.
     */
    public function messages(): array
    {
        return [
            'tenant_domain.required' => 'El dominio del tenant es obligatorio.',
            'tenant_domain.string'   => 'El dominio del tenant debe ser un texto válido.',
            'tenant_domain.max'      => 'El dominio del tenant no puede exceder los 191 caracteres.',

            'user_id.integer'        => 'El ID del usuario debe ser un número válido.',

            'content.required'       => 'El contenido del mensaje es obligatorio.',
            'content.string'         => 'El contenido del mensaje debe ser un texto válido.',
            'content.max'            => 'El contenido del mensaje es demasiado largo.',

            'sent_at.date'           => 'La fecha de envío debe ser una fecha válida.',
        ];
    }

    /**
     * Manejo de validación fallida.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
