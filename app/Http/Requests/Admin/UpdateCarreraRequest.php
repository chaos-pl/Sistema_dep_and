<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCarreraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        $carrera = $this->route('carrera');

        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('carreras', 'nombre')->ignore($carrera?->id),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre de la carrera',
        ];
    }
}
