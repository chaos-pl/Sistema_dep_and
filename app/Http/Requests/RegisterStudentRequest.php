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

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('matricula'))) {
            $this->merge(['matricula' => strtoupper($this->input('matricula'))]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::default()],
            'matricula' => ['bail', 'required', 'string', 'regex:/\AC?[0-9]{9}\z/', 'unique:estudiantes,matricula'],

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
            'matricula.string' => 'El número de cuenta debe contener 9 dígitos y, si eres estudiante de cambio, una C al inicio.',
            'matricula.regex' => 'Ingresa 9 dígitos, opcionalmente precedidos de una C para estudiantes de cambio, sin espacios ni otras letras.',
            'matricula.unique' => 'Este número de cuenta ya está registrado.',
        ];
    }
}
