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
            // Ni futura, ni anterior al día en que el trabajador recogió el producto
            'fecha_entrega' => [
                'required', 'date', 'before_or_equal:today',
                'after_or_equal:' . $asignacion->fecha_recogida->format('Y-m-d'),
            ],
            // Solo se puede dejar en una sede activa
            'sede_devolucion_id' => [
                'required',
                Rule::exists('sedes', 'id')->where('activa', true),
            ],
            // Al volver, el producto queda disponible o pasa a reparación (no puede volver "asignado" ni "de baja")
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
