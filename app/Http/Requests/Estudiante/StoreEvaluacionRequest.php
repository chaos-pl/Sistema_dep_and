<?php

namespace App\Http\Requests\Estudiante;

use Illuminate\Foundation\Http\FormRequest;

class StoreEvaluacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('estudiante');
    }

    public function rules(): array
    {
        return [
            'respuestas' => ['required', 'array'],
            'respuestas.*' => ['required', 'integer', 'min:0', 'max:3'],
        ];
    }

    public function messages(): array
    {
        return [
            'respuestas.required' => 'Debes contestar todas las preguntas.',
            'respuestas.array' => 'Las respuestas enviadas no son válidas.',
            'respuestas.*.required' => 'No dejes preguntas sin responder.',
            'respuestas.*.integer' => 'Cada respuesta debe ser un valor numérico.',
            'respuestas.*.min' => 'El valor mínimo permitido es 0.',
            'respuestas.*.max' => 'El valor máximo permitido es 3.',
        ];
    }
}
