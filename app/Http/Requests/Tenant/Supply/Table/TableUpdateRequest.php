<?php

namespace App\Http\Requests\Tenant\Supply\Table;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class TableUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normaliza datos antes de validar
     * Elimina el sufijo _edit de todos los campos
     */
    protected function prepareForValidation(): void
    {
        $data = $this->all();
        $normalized = [];

        foreach ($data as $key => $value) {
            if (str_ends_with($key, '_edit')) {
                $normalized[str_replace('_edit', '', $key)] = $value;
            }
        }

        if (!empty($normalized)) {
            $this->merge($normalized);
        }
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name_edit' => [
                'required',
                'string',
                'max:191',
                Rule::unique('tables', 'name')
                    ->ignore($this->route('id'))
                    ->where(function ($query) {
                        return $query->where('status', 'ACTIVO');
                    }),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name_edit.required' => 'El campo "nombre" es obligatorio.',
            'name_edit.string'   => 'El campo "nombre" debe ser una cadena de texto.',
            'name_edit.max'      => 'El campo "nombre" no debe exceder los 191 caracteres.',
            'name_edit.unique'   => 'Ya existe una mesa con este nombre en estado ACTIVO.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
