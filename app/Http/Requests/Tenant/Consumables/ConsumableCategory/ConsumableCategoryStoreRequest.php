<?php

namespace App\Http\Requests\Tenant\Consumables\ConsumableCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class ConsumableCategoryStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $data = $this->all();

        foreach ($data as $key => $value) {
            if (str_ends_with($key, '_mdlccategory')) {
                $newKey = str_replace('_mdlccategory', '', $key);
                $data[$newKey] = $value;
                unset($data[$key]);
            }
        }

        if (isset($data['name']) && $data['name'] !== null) {
            $data['name'] = mb_strtoupper($data['name'], 'UTF-8');
        }

        $this->replace($data);
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
                Rule::unique('consumable_categories')->where(function ($query) {
                    $query->where('status', '=', 'activo');
                }),
            ],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El campo nombre es obligatorio.',
            'name.string' => 'El campo nombre debe ser una cadena de texto.',
            'name.unique' => 'El nombre ya existe',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
