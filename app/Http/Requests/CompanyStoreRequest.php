<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyStoreRequest extends FormRequest
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
            'domain'                    => 'required',
            'ruc' => [
                'required',
                Rule::unique('companies')
                    ->where(function ($query) {
                        $query->where('status', 1);
                    }),
            ],
            'razon_social'              => 'required',
            'razon_social_abreviada'    => 'required',
            'plan_id'                   => 'required',

            'department' => 'required|exists:departments,id',
            'province'   => 'required|exists:provinces,id',
            'district'   => 'required|exists:districts,id',
        ];
    }

    public function messages(): array
    {
        return [
            'domain.required'                   => 'El nombre del dominio es requerido',
            'ruc.required'                      => 'El campo RUC es requerido',
            'ruc.unique'                        => 'El RUC ingresado ya está registrado para una empresa activa.',
            'razon_social.required'             => 'El campo razón social es requerido',
            'razon_social_abreviada.required'   => 'El campo razón social abreviada es requerido',
            'plan_id.required'                  => 'El plan es requerido',

            'department.required' => 'El departamento es requerido',
            'department.exists'   => 'El departamento seleccionado no es válido',

            'province.required' => 'La provincia es requerida',
            'province.exists'   => 'La provincia seleccionada no es válida',

            'district.required' => 'El distrito es requerido',
            'district.exists'   => 'El distrito seleccionado no es válido',
        ];
    }
}
