<?php

namespace App\Http\Requests\Tenant\Supply\TypeDish;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class TypeDishStoreRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:191',
                Rule::unique('types_dish')->where(function ($query) {
                    return $query->where('status', 'ACTIVO');
                }),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El campo "descripción" es obligatorio.',
            'name.string'   => 'El campo "descripción" debe ser una cadena de texto.',
            'name.max'      => 'El campo "descripción" no debe exceder los 191 caracteres.',
            'name.unique'   => 'Ya existe una mesa con este nombre en estado ACTIVO.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
