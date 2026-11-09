<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AsignarProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $producto = $this->route('producto');

        // Última devolución de este producto: la nueva recogida no puede ser anterior
        $ultimaEntrega = $producto->asignaciones()->max('fecha_entrega');

        $reglasFecha = ['required', 'date', 'before_or_equal:today'];
        if ($ultimaEntrega) {
            $reglasFecha[] = 'after_or_equal:' . $ultimaEntrega;
        }

        return [
            'trabajador_id'  => [
                'required',
                Rule::exists('trabajadores', 'id')->where('activo', true)->whereNull('deleted_at'),
            ],
            'tipo'           => ['required', Rule::in(['temporal', 'permanente'])],
            'fecha_recogida' => $reglasFecha,
            'observaciones'  => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'trabajador_id'  => 'trabajador',
            'fecha_recogida' => 'fecha de recogida',
        ];
    }

    public function messages(): array
    {
        return [
            'fecha_recogida.after_or_equal' => 'La fecha de recogida no puede ser anterior a la última devolución de este producto.',
            'trabajador_id.exists'          => 'El trabajador seleccionado no existe o no está activo.',
        ];
    }
}
