<?php

namespace App\Http\Requests\Psicologo;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiagnosticoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('psicologo');
    }

    public function rules(): array
    {
        return [
            'evaluacion_id' => ['required', 'exists:evaluaciones,id'],
            'impresion_diagnostica' => ['required', 'string'],
            'retroalimentacion_estudiante' => ['nullable', 'string'],
            'requiere_derivacion' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'evaluacion_id' => 'evaluación',
            'impresion_diagnostica' => 'impresión diagnóstica',
            'retroalimentacion_estudiante' => 'retroalimentación al estudiante',
            'requiere_derivacion' => 'requiere derivación',
        ];
    }
}
