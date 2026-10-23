<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::default()],
            'matricula' => ['bail', 'required', 'string', 'regex:/\A[0-9]{9}\z/', 'unique:estudiantes,matricula'],

            'nombre' => ['required', 'string', 'max:100'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'apellido_materno' => ['nullable', 'string', 'max:100'],
            'fecha_nacimiento' => ['required', 'date'],
            'genero' => ['required', 'in:masculino,femenino,otro,prefiero_no_decirlo'],
            'telefono' => ['nullable', new \App\Rules\PhoneNumber],
        ];
    }

    public function messages(): array
    {
        return [
            'matricula.required' => 'El número de cuenta es obligatorio.',
            'matricula.string' => 'El número de cuenta debe contener exactamente 9 dígitos.',
            'matricula.regex' => 'El número de cuenta debe contener exactamente 9 dígitos, sin letras ni espacios.',
            'matricula.unique' => 'Este número de cuenta ya está registrado.',
        ];
    }
}
