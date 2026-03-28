<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAppearanceRequest extends FormRequest
{
    protected $errorBag = 'updateAppearance';

    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'avatar_icon' => [
                'required',
                'string',
                Rule::in(array_keys(config('appearance.avatar_icons', []))),
            ],
            'theme' => [
                'required',
                'string',
                Rule::in(array_keys(config('appearance.themes', []))),
            ],
            'accent_color' => [
                'required',
                'string',
                Rule::in(array_keys(config('appearance.accent_colors', []))),
            ],
            'density' => [
                'required',
                'string',
                Rule::in(array_keys(config('appearance.density', []))),
            ],
            'reduced_motion' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'avatar_icon.required' => 'Debes seleccionar un icono.',
            'avatar_icon.in' => 'El icono seleccionado no es válido.',
            'theme.required' => 'Debes seleccionar un tema.',
            'theme.in' => 'El tema seleccionado no es válido.',
            'accent_color.required' => 'Debes seleccionar un color de acento.',
            'accent_color.in' => 'El color seleccionado no es válido.',
            'density.required' => 'Debes seleccionar un modo de interfaz.',
            'density.in' => 'El modo de interfaz no es válido.',
        ];
    }
}
