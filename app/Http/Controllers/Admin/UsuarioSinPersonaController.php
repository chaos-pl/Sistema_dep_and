<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class UsuarioSinPersonaController extends Controller
{
    /**
     * Lista usuarios que no tienen una persona vinculada.
     */
    public function index()
    {
        $usuarios = User::whereDoesntHave('persona')
            ->with('roles')
            ->latest()
            ->paginate(15);

        return view('admin.usuarios-sin-persona.index', compact('usuarios'));
    }

    /**
     * Formulario para vincular persona a un usuario.
     */
    public function edit(User $user)
    {
        if ($user->persona) {
            Alert::info('Ya vinculado', 'Este usuario ya tiene una persona vinculada.');
            return redirect()->route('admin.usuarios-sin-persona.index');
        }

        return view('admin.usuarios-sin-persona.edit', compact('user'));
    }

    /**
     * Vincular (crear) una persona para el usuario.
     */
    public function update(Request $request, User $user)
    {
        if ($user->persona) {
            Alert::info('Ya vinculado', 'Este usuario ya tiene una persona vinculada.');
            return redirect()->route('admin.usuarios-sin-persona.index');
        }

        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'fecha_nacimiento' => 'required|date',
            'genero' => 'required|in:masculino,femenino,otro,prefiero_no_decirlo',
            'telefono' => 'nullable|string|max:20',
        ]);

        DB::transaction(function () use ($request, $user) {
            Persona::create([
                'user_id' => $user->id,
                'nombre' => $request->nombre,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'genero' => $request->genero,
                'telefono' => $request->telefono,
            ]);

            $nombreCompleto = trim($request->nombre . ' ' . $request->apellido_paterno . ' ' . ($request->apellido_materno ?? ''));
            $user->update(['name' => $nombreCompleto]);
        });

        Alert::success('Persona vinculada', 'Los datos personales fueron vinculados correctamente al usuario.');
        return redirect()->route('admin.usuarios-sin-persona.index');
    }
}
