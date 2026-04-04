<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCarreraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255', Rule::unique('carreras', 'nombre')],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre de la carrera',
        ];
    }
}
