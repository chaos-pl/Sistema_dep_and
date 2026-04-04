<?php

namespace App\Http\Requests\Estudiante;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('estudiante');
    }

    public function rules(): array
    {
        return [
            'texto_ingresado' => ['required', 'string', 'min:10', 'max:3000'],
        ];
    }

    public function messages(): array
    {
        return [
            'texto_ingresado.required' => 'Debes escribir una entrada en tu diario.',
            'texto_ingresado.min' => 'La entrada debe tener al menos 10 caracteres.',
            'texto_ingresado.max' => 'La entrada no puede exceder 3000 caracteres.',
        ];
    }
}
