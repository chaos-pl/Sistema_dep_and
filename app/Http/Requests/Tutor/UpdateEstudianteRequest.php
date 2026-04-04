<?php

namespace App\Http\Requests\Tutor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEstudianteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('tutor');
    }

    public function rules(): array
    {
        $estudiante = $this->route('estudiante');
        $persona = $estudiante?->persona;
        $user = $persona?->user;

        return [
            'nombre' => ['required', 'string', 'max:100'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'apellido_materno' => ['nullable', 'string', 'max:100'],
            'fecha_nacimiento' => ['required', 'date'],
            'genero' => ['required', Rule::in(['masculino', 'femenino', 'otro', 'prefiero_no_decirlo'])],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user?->id),
            ],
            'matricula' => [
                'required',
                'string',
                'max:50',
                Rule::unique('estudiantes', 'matricula')->ignore($estudiante?->id),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'apellido_paterno' => 'apellido paterno',
            'apellido_materno' => 'apellido materno',
            'fecha_nacimiento' => 'fecha de nacimiento',
            'genero' => 'género',
            'telefono' => 'teléfono',
            'email' => 'correo electrónico',
            'matricula' => 'matrícula',
            'password' => 'contraseña',
        ];
    }
}
