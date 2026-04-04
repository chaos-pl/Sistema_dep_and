<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreGrupoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'carrera_id' => ['required', 'exists:carreras,id'],
            'tutor_id' => ['required', 'exists:tutores,id'],
            'nombre' => ['required', 'string', 'max:50'],
            'periodo' => ['required', 'string', 'max:20'],
        ];
    }

    public function attributes(): array
    {
        return [
            'carrera_id' => 'carrera',
            'tutor_id' => 'tutor',
            'nombre' => 'nombre del grupo',
            'periodo' => 'periodo',
        ];
    }
}
