<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\DeleteProfileAccountRequest;
use App\Http\Requests\Profile\UpdateAppearanceRequest;
use App\Http\Requests\Profile\UpdateProfileInformationRequest;
use App\Http\Requests\Profile\UpdateProfilePasswordRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use RealRashid\SweetAlert\Facades\Alert;

class ProfileController extends Controller
{
    public function edit(): View
    {
        // Cargamos los roles y también el expediente (persona) para que las vistas tengan la información
        $user = auth()->user()->loadMissing(['roles', 'persona']);

        return view('perfil.index', compact('user'));
    }

    public function update(UpdateProfileInformationRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $emailChanged = $user->email !== $validated['email'];

        $user->fill($validated);

        if ($emailChanged && $user instanceof MustVerifyEmail) {
            $user->email_verified_at = null;
        }

        $user->save();

        Alert::success('Credenciales actualizadas', 'Tus datos de acceso se actualizaron correctamente.');

        return back();
    }

    /**
     * Actualiza o crea el expediente personal (Demográfico) del usuario.
     */
    public function updatePersona(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'apellido_materno' => ['nullable', 'string', 'max:100'],
            'fecha_nacimiento' => ['required', 'date'],
            'genero' => ['required', 'in:masculino,femenino,otro,prefiero_no_decirlo'],
            'telefono' => ['nullable', 'string', 'max:20'],
        ]);

        // Guardamos en la tabla personas y la relacionamos con este user_id
        $request->user()->persona()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $validated
        );

        Alert::success('Expediente actualizado', 'Tus datos personales se guardaron correctamente.');

        return back();
    }

    public function updatePassword(UpdateProfilePasswordRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => Hash::make($request->validated()['password']),
        ]);

        Alert::success('Contraseña actualizada', 'Tu contraseña se cambió correctamente.');

        return back();
    }

    public function updateAppearance(UpdateAppearanceRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->update([
            'avatar_icon' => $request->avatar_icon,
            'appearance_settings' => [
                'theme' => $request->theme,
                'accent_color' => $request->accent_color,
                'density' => $request->density,
                'reduced_motion' => (bool) $request->boolean('reduced_motion'),
            ],
        ]);

        Alert::success('Apariencia actualizada', 'Tus preferencias visuales se guardaron correctamente.');

        return back();
    }

    public function destroy(DeleteProfileAccountRequest $request): RedirectResponse
    {
        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Alert::success('Cuenta eliminada', 'Tu cuenta fue eliminada permanentemente.');

        return redirect()->route('login');
    }
}
