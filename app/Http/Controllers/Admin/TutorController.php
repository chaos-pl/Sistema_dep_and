<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTutorRequest;
use App\Http\Requests\Admin\UpdateTutorRequest;
use App\Models\Persona;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class TutorController extends Controller
{
    public function index()
    {
        $tutores = Tutor::with(['persona.user'])
            ->withCount('grupos')
            ->latest()
            ->paginate(10);

        return view('admin.tutores.index', compact('tutores'));
    }

    public function create()
    {
        return view('admin.tutores.create');
    }

    public function store(StoreTutorRequest $request)
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

            $user->assignRole('tutor');

            $persona = Persona::create([
                'user_id' => $user->id,
                'nombre' => $request->nombre,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'genero' => $request->genero,
                'telefono' => $request->telefono,
            ]);

            Tutor::create([
                'persona_id' => $persona->id,
                'numero_empleado' => $request->numero_empleado,
            ]);
        });

        Alert::success('Tutor registrado', 'El tutor fue dado de alta correctamente.');

        return redirect()->route('admin.tutores.index');
    }

    /**
     * Muestra el detalle de un tutor específico.
     */
    public function show(Tutor $tutor)
    {
        // Cargamos la información personal y del usuario asociado
        $tutor->loadMissing(['persona.user']);

        // Verificamos de forma segura si el modelo Tutor tiene la relación 'grupos'
        if (method_exists($tutor, 'grupos')) {
            $tutor->loadCount('grupos');
        } else {
            // Valor por defecto en caso de que la relación aún no exista en el modelo
            $tutor->grupos_count = 0;
        }

        return view('admin.tutores.show', compact('tutor'));
    }

    public function edit(Tutor $tutor)
    {
        $tutor->load(['persona.user']);

        return view('admin.tutores.edit', compact('tutor'));
    }

    public function update(UpdateTutorRequest $request, Tutor $tutor)
    {
        $tutor->load(['persona.user']);

        DB::transaction(function () use ($request, $tutor) {
            $persona = $tutor->persona;
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

            $tutor->update([
                'numero_empleado' => $request->numero_empleado,
            ]);
        });

        Alert::success('Tutor actualizado', 'Los datos del tutor fueron actualizados.');

        return redirect()->route('admin.tutores.index');
    }

    public function destroy(Tutor $tutor)
    {
        if (method_exists($tutor, 'grupos')) {
            $tutor->loadCount('grupos');
            $gruposCount = $tutor->grupos_count;
        } else {
            $gruposCount = 0;
        }

        if ($gruposCount > 0) {
            Alert::warning('No permitido', 'No puedes eliminar un tutor que ya tiene grupos asignados.');
            return redirect()->route('admin.tutores.index');
        }

        DB::transaction(function () use ($tutor) {
            $persona = $tutor->persona;
            $user = $persona?->user;

            $tutor->delete();

            if ($persona) {
                $persona->delete();
            }

            if ($user) {
                $user->delete();
            }
        });

        Alert::success('Tutor eliminado', 'El tutor fue eliminado correctamente.');

        return redirect()->route('admin.tutores.index');
    }
}
