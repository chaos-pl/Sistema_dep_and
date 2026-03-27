<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePersonaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $personaId = $this->route('persona')?->id ?? $this->route('persona');

        return [
            'user_id' => [
                'nullable',
                'exists:users,id',
                Rule::unique('personas', 'user_id')->ignore($personaId),
            ],
            'nombre' => ['required', 'string', 'max:100'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'apellido_materno' => ['nullable', 'string', 'max:100'],
            'fecha_nacimiento' => ['required', 'date'],
            'genero' => ['required', 'in:masculino,femenino,otro,prefiero_no_decirlo'],
            'telefono' => ['nullable', 'string', 'max:20'],
        ];
    }
}
