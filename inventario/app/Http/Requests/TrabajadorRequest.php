<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TrabajadorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('dni')) {
            $this->merge(['dni' => strtoupper(str_replace([' ', '-'], '', $this->dni))]);
        }
    }

    public function rules(): array
    {
        $trabajador = $this->route('trabajador');

        return [
            'nombre'    => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:150'],
            'dni'       => [
                'nullable', 'regex:/^[0-9XYZ][0-9]{7}[A-Z]$/',
                Rule::unique('trabajadores', 'dni')->ignore($trabajador),
            ],
            'email'     => ['nullable', 'email', 'max:255'],
            'telefono'  => ['nullable', 'string', 'max:20'],
            'sede_id'   => ['nullable', 'exists:sedes,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'dni.regex' => 'El DNI/NIE no tiene un formato válido (ej.: 12345678Z o X1234567L).',
        ];
    }
}
