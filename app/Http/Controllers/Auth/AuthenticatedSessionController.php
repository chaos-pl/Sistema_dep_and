<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use RealRashid\SweetAlert\Facades\Alert;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            Alert::warning('Acceso denegado', 'Las credenciales proporcionadas no son correctas.');

            return back()->withErrors([
                'email' => 'Las credenciales proporcionadas no son correctas.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        if (!auth()->user()->acepto_consentimiento) {
            Alert::info('Consentimiento requerido', 'Antes de continuar debes aceptar el consentimiento informado.');

            return redirect()->route('consentimiento.create');
        }

        Alert::success('Bienvenido', 'Inicio de sesión correcto en PROMETEO.');

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
