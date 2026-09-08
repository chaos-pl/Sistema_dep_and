<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDass21Request extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta petición.
     */
    public function authorize(): bool
    {
        // Asumimos que el middleware de autenticación ya verifica que el usuario esté logueado.
        return true;
    }

    /**
     * Reglas de validación.
     */
    public function rules(): array
    {
        return [
            // Validamos que 'answers' sea un array y tenga exactamente 21 respuestas
            'answers'   => ['required', 'array', 'size:21'],

            // Validamos que CADA respuesta sea un entero entre 0 y 3
            'answers.*' => ['required', 'integer', 'min:0', 'max:3'],
        ];
    }

    /**
     * Mensajes personalizados de error (Opcional, pero mejora la UX).
     */
    public function messages(): array
    {
        return [
            'answers.size' => 'Debes responder exactamente las 21 preguntas del tamizaje.',
            'answers.*.required' => 'Todas las preguntas deben ser respondidas.',
            'answers.*.min' => 'El valor de la respuesta no es válido.',
            'answers.*.max' => 'El valor de la respuesta no es válido.',
        ];
    }
}
