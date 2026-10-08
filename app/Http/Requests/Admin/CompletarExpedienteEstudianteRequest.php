<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CompletarExpedienteEstudianteRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && ($user->hasRole('admin')
            || ($user->hasRole('control_escolar') && $user->can('estudiantes.asignar_grupo')));
    }

    public function rules(): array
    {
        return [
            'grupo_id' => ['required', 'exists:grupos,id'],
            'matricula' => ['required', 'string', 'max:50', 'unique:estudiantes,matricula'],
        ];
    }

    public function attributes(): array
    {
        return [
            'grupo_id' => 'grupo',
            'matricula' => 'matrícula',
        ];
    }
}
