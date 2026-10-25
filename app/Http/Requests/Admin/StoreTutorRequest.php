<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTutorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->hasRole('admin')
            || ($this->user()->hasRole('control_escolar') && $this->user()->can('tutores.crear')));
    }

    public function rules(): array
    {
        if ($this->input('tipo_registro') === 'existente') {
            return [
                'tipo_registro' => ['required', Rule::in(['existente'])],
                'persona_id' => ['bail', 'required', 'integer', 'exists:personas,id', 'unique:tutores,persona_id', function ($attribute, $value, $fail) {
                    if (! \App\Models\Persona::whereKey($value)->whereHas('user')->exists()) {
                        $fail('Selecciona una persona con cuenta de usuario.');
                    }
                }],
                'telefono' => ['nullable', new \App\Rules\PhoneNumber],
                'numero_empleado' => ['required', 'string', 'max:50', 'unique:tutores,numero_empleado'],
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
            'numero_empleado' => ['required', 'string', 'max:50', 'unique:tutores,numero_empleado'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', \Illuminate\Validation\Rules\Password::defaults(), 'confirmed'],
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
            'numero_empleado' => 'número de empleado',
            'email' => 'correo electrónico',
            'password' => 'contraseña',
        ];
    }
}
