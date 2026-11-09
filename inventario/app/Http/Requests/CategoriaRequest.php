<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoriaRequest extends FormRequest
{
     public function authorize(): bool
    {
        return true;
    }

    // Normaliza el prefijo antes de validar: "  ord " → "ORD"
    protected function prepareForValidation(): void
    {
        if ($this->has('prefijo')) {
            $this->merge(['prefijo' => strtoupper(trim($this->prefijo))]);
        }
    }

    public function rules(): array
    {
        $categoria = $this->route('categoria'); // null al crear, el modelo al editar

        return [
            'nombre' => [
                'required', 'string', 'max:100',
                Rule::unique('categorias', 'nombre')->ignore($categoria),
            ],
            'prefijo' => [
                'required', 'regex:/^[A-Z]{2,5}$/',
                Rule::unique('categorias', 'prefijo')->ignore($categoria),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'prefijo.regex' => 'El prefijo debe tener entre 2 y 5 letras, sin números ni símbolos.',
        ];
    }
}
