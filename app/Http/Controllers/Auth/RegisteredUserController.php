<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterStudentRequest;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use RealRashid\SweetAlert\Facades\Alert;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterStudentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = null;

        DB::transaction(function () use ($data, &$user) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $user->assignRole('estudiante');

            Persona::create([
                'user_id' => $user->id,
                'nombre' => $data['nombre'],
                'apellido_paterno' => $data['apellido_paterno'],
                'apellido_materno' => $data['apellido_materno'] ?? null,
                'fecha_nacimiento' => $data['fecha_nacimiento'],
                'genero' => $data['genero'],
                'telefono' => $data['telefono'] ?? null,
            ]);
        });

        event(new Registered($user));

        Auth::login($user);

        Alert::success('Registro exitoso', 'Tu cuenta fue creada correctamente. Ahora revisa el consentimiento informado.');

        return redirect()->route('consentimiento.create');
    }
}
