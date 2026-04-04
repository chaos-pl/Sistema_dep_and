<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTutorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        $tutor = $this->route('tutor');
        $persona = $tutor?->persona;
        $user = $persona?->user;

        return [
            'nombre' => ['required', 'string', 'max:100'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'apellido_materno' => ['nullable', 'string', 'max:100'],
            'fecha_nacimiento' => ['required', 'date'],
            'genero' => ['required', Rule::in(['masculino', 'femenino', 'otro', 'prefiero_no_decirlo'])],
            'telefono' => ['nullable', 'string', 'max:20'],
            'numero_empleado' => [
                'required',
                'string',
                'max:50',
                Rule::unique('tutores', 'numero_empleado')->ignore($tutor?->id),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user?->id),
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
            'numero_empleado' => 'número de empleado',
            'email' => 'correo electrónico',
            'password' => 'contraseña',
        ];
    }
}
