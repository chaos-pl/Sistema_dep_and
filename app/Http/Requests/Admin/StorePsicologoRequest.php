<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePsicologoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        if ($this->input('tipo_registro') === 'existente') {
            return [
                'tipo_registro' => ['required', Rule::in(['existente'])],
                'persona_id' => ['required', 'integer', 'exists:personas,id', 'unique:psicologos,persona_id', function ($attribute, $value, $fail) {
                    if (! \App\Models\Persona::whereKey($value)->whereHas('user')->exists()) {
                        $fail('Selecciona una persona con una cuenta de usuario activa.');
                    }
                }],
                'telefono' => ['nullable', new \App\Rules\PhoneNumber],
                'cedula_profesional' => ['required', 'string', 'max:50', 'unique:psicologos,cedula_profesional'],
            ];
        }

        return [
            'tipo_registro' => ['sometimes', Rule::in(['nueva'])],
            'persona_id' => ['prohibited'],
            'nombre' => ['required', 'string', 'max:100'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'apellido_materno' => ['nullable', 'string', 'max:100'],
            'fecha_nacimiento' => ['required', 'date'],
            'genero' => ['required', Rule::in(['masculino', 'femenino', 'otro', 'prefiero_no_decirlo'])],
            'telefono' => ['nullable', new \App\Rules\PhoneNumber],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', \Illuminate\Validation\Rules\Password::defaults(), 'confirmed'],
            'cedula_profesional' => ['required', 'string', 'max:50', 'unique:psicologos,cedula_profesional'],
        ];
    }

    public function attributes(): array
    {
        return [
            'persona_id' => 'persona',
            'nombre' => 'nombre',
            'apellido_paterno' => 'apellido paterno',
            'apellido_materno' => 'apellido materno',
            'fecha_nacimiento' => 'fecha de nacimiento',
            'genero' => 'género',
            'telefono' => 'teléfono',
            'email' => 'correo electrónico',
            'password' => 'contraseña',
            'cedula_profesional' => 'cédula profesional',
        ];
    }
}
