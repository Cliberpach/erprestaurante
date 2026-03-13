<?php

namespace App\Http\Requests\Tenant\Consumables\Purchase;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class PurchaseStoreRequest extends FormRequest
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
            'fecha_registro' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'in:' . now()->toDateString(),
            ],
            'fecha_entrega' => [
                'required',
                'date',
                'date_format:Y-m-d',
            ],
            'proveedor' => [
                'required',
                'exists:suppliers,id',
                Rule::exists('suppliers', 'id')->where('estado', 'ACTIVO'),
            ],
            'tipo_doc' => [
                'required',
                Rule::in(['BOLETA', 'FACTURA']),
            ],
            'igv_chk' => [
                'nullable',
                'numeric',
                Rule::exists('companies', 'igv'),
            ],
            'serie' => [
                'required',
                'string',
            ],
            'numero' => [
                'required',
                'string',
            ],
            'observation' => [
                'nullable',
                'string',
                'max:200',
            ],
            'moneda' => [
                'required',
                Rule::in(['PEN', 'USD']),
            ],
            'cost_center' => [
                'required',
                Rule::exists('cost_center', 'id')->where('status', 'ACTIVO'),
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'fecha_registro.required'       => 'La fecha de registro es obligatoria.',
            'fecha_registro.date'           => 'La fecha de registro debe ser una fecha válida.',
            'fecha_registro.date_format'    => 'La fecha de registro debe tener el formato YYYY-MM-DD.',
            'fecha_registro.in'             => 'La fecha de registro debe ser la fecha actual.',

            'fecha_entrega.required'        => 'La fecha de entrega es obligatoria.',
            'fecha_entrega.date'            => 'La fecha de entrega debe ser una fecha válida.',
            'fecha_entrega.date_format'     => 'La fecha de entrega debe tener el formato YYYY-MM-DD.',

            'proveedor.required'            => 'El proveedor es obligatorio.',
            'proveedor.exists'              => 'El proveedor no existe o no está activo.',

            'tipo_doc.required'             => 'El tipo de documento es obligatorio.',
            'tipo_doc.in'                   => 'El tipo de documento debe ser BOLETA o FACTURA.',

            'igv_chk.numeric'                   => 'El IGV debe ser un número.',
            'igv_chk.exists'                    => 'El valor del IGV no coincide con el registrado en la tabla de empresas.',

            'serie.required'    => 'La serie es obligatoria.',

            'numero.required'   => 'El número es obligatorio.',

            'observation.max'   => 'La observación no debe exceder los 200 caracteres.',

            'moneda.required'   => 'La moneda es obligatoria.',
            'moneda.in'         => 'La moneda debe ser PEN o USD.',

            'cost_center.required' => 'El centro de costo es obligatorio.',
            'cost_center.exists'   => 'El centro de costo no existe.',
        ];
    }


    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
