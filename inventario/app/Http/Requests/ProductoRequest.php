<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductoRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

         $producto = $this->route('producto'); // null al crear

        return [
            'nombre'       => ['required', 'string', 'max:255'],
            'marca'        => ['nullable', 'string', 'max:100'],
            'modelo'       => ['nullable', 'string', 'max:100'],
            'numero_serie' => [
                'nullable', 'string', 'max:100',
                Rule::unique('productos', 'numero_serie')->ignore($producto),
            ],
            'descripcion'  => ['nullable', 'string', 'max:2000'],
            'sede_id'      => ['required', 'exists:sedes,id'],
            'fecha_alta'   => ['nullable', 'date', 'before_or_equal:today'],

            // La categoría solo se elige al crear: de ella depende la referencia
            'categoria_id' => $producto
                ? ['exclude']
                : ['required', 'exists:categorias,id'],

            // Mientras lo tenga un trabajador, el estado lo controla la asignación
            'estado' => $producto?->asignacionActiva
                ? ['exclude']
                : ['required', Rule::in(['disponible', 'reparacion', 'baja'])],
        ];
    }
}
