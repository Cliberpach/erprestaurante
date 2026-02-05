<?php

namespace App\Http\Requests\Tenant\Sale;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;

class SaleCreditNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'motive' => [
                'required',
                'string',
                'max:160',
            ],
            'observation' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'motive.required' => 'El motivo de la nota de crédito es obligatorio.',
            'motive.string'   => 'El motivo debe ser un texto válido.',
            'motive.max'      => 'El motivo no debe exceder los 160 caracteres.',

            'observation.string' => 'La observación debe ser un texto válido.',
            'observation.max'    => 'La observación no debe exceder los 255 caracteres.',
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
