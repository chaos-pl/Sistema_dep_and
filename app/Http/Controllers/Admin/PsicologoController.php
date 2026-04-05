<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePsicologoRequest;
use App\Http\Requests\Admin\UpdatePsicologoRequest;
use App\Models\Persona;
use App\Models\Psicologo;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class PsicologoController extends Controller
{
    public function index()
    {
        $psicologos = Psicologo::with(['persona.user'])
            ->latest()
            ->paginate(10);

        return view('admin.psicologos.index', compact('psicologos'));
    }

    public function create()
    {
        return view('admin.psicologos.create');
    }

    public function store(StorePsicologoRequest $request)
    {
        DB::transaction(function () use ($request) {
            $nombreCompleto = trim(
                $request->nombre . ' ' .
                $request->apellido_paterno . ' ' .
                ($request->apellido_materno ?? '')
            );

            $user = User::create([
                'name' => $nombreCompleto,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole('psicologo');

            $persona = Persona::create([
                'user_id' => $user->id,
                'nombre' => $request->nombre,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'genero' => $request->genero,
                'telefono' => $request->telefono,
            ]);

            Psicologo::create([
                'persona_id' => $persona->id,
                'cedula_profesional' => $request->cedula_profesional,
            ]);
        });

        Alert::success('Psicólogo registrado', 'El psicólogo fue dado de alta correctamente.');

        return redirect()->route('admin.psicologos.index');
    }

    /**
     * Muestra el detalle de un psicólogo específico.
     */
    public function show(Psicologo $psicologo)
    {
        // Cargamos la información personal y de usuario para evitar errores en la vista
        $psicologo->loadMissing(['persona.user']);

        return view('admin.psicologos.show', compact('psicologo'));
    }

    public function edit(Psicologo $psicologo)
    {
        $psicologo->load(['persona.user']);

        return view('admin.psicologos.edit', compact('psicologo'));
    }

    public function update(UpdatePsicologoRequest $request, Psicologo $psicologo)
    {
        $psicologo->load(['persona.user']);

        DB::transaction(function () use ($request, $psicologo) {
            $persona = $psicologo->persona;
            $user = $persona->user;

            $nombreCompleto = trim(
                $request->nombre . ' ' .
                $request->apellido_paterno . ' ' .
                ($request->apellido_materno ?? '')
            );

            $user->update([
                'name' => $nombreCompleto,
                'email' => $request->email,
            ]);

            if ($request->filled('password')) {
                $user->update([
                    'password' => Hash::make($request->password),
                ]);
            }

            $persona->update([
                'nombre' => $request->nombre,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'genero' => $request->genero,
                'telefono' => $request->telefono,
            ]);

            $psicologo->update([
                'cedula_profesional' => $request->cedula_profesional,
            ]);
        });

        Alert::success('Psicólogo actualizado', 'Los datos del psicólogo fueron actualizados correctamente.');

        return redirect()->route('admin.psicologos.index');
    }

    public function destroy(Psicologo $psicologo)
    {
        DB::transaction(function () use ($psicologo) {
            $persona = $psicologo->persona;
            $user = $persona?->user;

            $psicologo->delete();

            if ($persona) {
                $persona->delete();
            }

            if ($user) {
                $user->delete();
            }
        });

        Alert::success('Psicólogo eliminado', 'El psicólogo fue eliminado correctamente.');

        return redirect()->route('admin.psicologos.index');
    }
}
