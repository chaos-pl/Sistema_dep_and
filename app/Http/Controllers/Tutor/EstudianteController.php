<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tutor\StoreEstudianteRequest;
use App\Http\Requests\Tutor\UpdateEstudianteRequest;
use App\Models\Estudiante;
use App\Models\Grupo;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class EstudianteController extends Controller
{
    public function create(Grupo $grupo)
    {
        $this->verificarGrupoDelTutor($grupo);

        return view('tutor.estudiantes.create', compact('grupo'));
    }

    public function store(StoreEstudianteRequest $request, Grupo $grupo)
    {
        $this->verificarGrupoDelTutor($grupo);

        DB::transaction(function () use ($request, $grupo) {
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

            $user->assignRole('estudiante');

            $persona = Persona::create([
                'user_id' => $user->id,
                'nombre' => $request->nombre,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'genero' => $request->genero,
                'telefono' => $request->telefono,
            ]);

            Estudiante::create([
                'persona_id' => $persona->id,
                'matricula' => $request->matricula,
                'grupo_id' => $grupo->id,
                'codigo_anonimo' => $this->generarCodigoAnonimo(),
            ]);
        });

        Alert::success('Estudiante registrado', 'El estudiante fue dado de alta correctamente.');

        return redirect()->route('tutor.grupos.show', $grupo->id);
    }

    public function edit(Estudiante $estudiante)
    {
        $this->verificarEstudianteDelTutor($estudiante);

        $estudiante->load(['persona.user', 'grupo']);

        return view('tutor.estudiantes.edit', compact('estudiante'));
    }

    public function update(UpdateEstudianteRequest $request, Estudiante $estudiante)
    {
        $this->verificarEstudianteDelTutor($estudiante);

        DB::transaction(function () use ($request, $estudiante) {
            $persona = $estudiante->persona;
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

            $estudiante->update([
                'matricula' => $request->matricula,
            ]);
        });

        Alert::success('Estudiante actualizado', 'Los datos del estudiante fueron actualizados correctamente.');

        return redirect()->route('tutor.grupos.show', $estudiante->grupo_id);
    }

    private function verificarGrupoDelTutor(Grupo $grupo): void
    {
        $tutor = $this->obtenerTutorAutenticado();

        abort_unless((int) $grupo->tutor_id === (int) $tutor->id, 403, 'No puedes registrar estudiantes en un grupo ajeno.');
    }

    private function verificarEstudianteDelTutor(Estudiante $estudiante): void
    {
        $tutor = $this->obtenerTutorAutenticado();

        $estudiante->loadMissing('grupo');

        abort_unless((int) $estudiante->grupo->tutor_id === (int) $tutor->id, 403, 'No puedes editar estudiantes de un grupo ajeno.');
    }

    private function obtenerTutorAutenticado()
    {
        $user = Auth::user();
        $persona = $user->persona;

        abort_unless($persona && $persona->tutor, 403, 'No existe un tutor vinculado a este usuario.');

        return $persona->tutor;
    }

    private function generarCodigoAnonimo(): string
    {
        do {
            $codigo = 'EST-' . strtoupper(Str::random(8));
        } while (Estudiante::where('codigo_anonimo', $codigo)->exists());

        return $codigo;
    }
}
