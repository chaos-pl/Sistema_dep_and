<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('permisos.editar');
    }

    public function rules(): array
    {
        $permiso = $this->route('permiso');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions', 'name')->ignore($permiso->id),
            ],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,name'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del permiso es obligatorio.',
            'name.unique' => 'Ese permiso ya existe.',
            'roles.array' => 'Los roles deben enviarse como una lista válida.',
            'roles.*.exists' => 'Uno de los roles seleccionados no existe.',
        ];
    }
}
