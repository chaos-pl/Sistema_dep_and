<?php

namespace App\Http\Requests\Tutor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEstudianteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('tutor');
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'apellido_materno' => ['nullable', 'string', 'max:100'],
            'fecha_nacimiento' => ['required', 'date'],
            'genero' => ['required', Rule::in(['masculino', 'femenino', 'otro', 'prefiero_no_decirlo'])],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'matricula' => ['required', 'string', 'max:50', 'unique:estudiantes,matricula'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
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
