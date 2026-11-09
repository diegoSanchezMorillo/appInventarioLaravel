<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DevolverProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $asignacion = $this->route('asignacion');

        return [
            'fecha_entrega' => [
                'required', 'date', 'before_or_equal:today',
                'after_or_equal:' . $asignacion->fecha_recogida->format('Y-m-d'),
            ],
            'sede_devolucion_id' => [
                'required',
                Rule::exists('sedes', 'id')->where('activa', true),
            ],
            'estado'        => ['required', Rule::in(['disponible', 'reparacion'])],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'fecha_entrega'      => 'fecha de entrega',
            'sede_devolucion_id' => 'sede',
        ];
    }

    public function messages(): array
    {
        return [
            'fecha_entrega.after_or_equal' => 'La fecha de entrega no puede ser anterior a la de recogida.',
        ];
    }
}
