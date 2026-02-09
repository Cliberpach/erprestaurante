<?php

namespace App\Http\Requests\Tenant\Maintenance\CostCenter;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class CostCenterStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Limpia y normaliza datos antes de validar
     */
    protected function prepareForValidation(): void
    {
        $data = [];

        foreach ($this->all() as $key => $value) {

            if (is_string($value)) {
                $value = mb_strtoupper(trim($value), 'UTF-8');
            }

            if (str_ends_with($key, '_mdlcostcenter')) {
                $newKey = str_replace('_mdlcostcenter', '', $key);
                $data[$newKey] = $value;
            } else {
                $data[$key] = $value;
            }
        }

        $this->replace($data);
    }



    /**
     * Reglas de validación
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:200',
            ],
        ];
    }

    /**
     * Mensajes personalizados
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del centro de costo es obligatorio.',
            'name.string'   => 'El nombre del centro de costo debe ser texto.',
            'name.max'      => 'El nombre del centro de costo no debe superar los 200 caracteres.',
        ];
    }

    /**
     * Error en formato JSON
     */
    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
