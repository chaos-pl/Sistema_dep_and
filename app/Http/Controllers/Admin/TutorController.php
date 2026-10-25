<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTutorRequest;
use App\Http\Requests\Admin\UpdateTutorRequest;
use App\Services\ExistingTutorRegistrationService;
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
        $tutores = Tutor::with(['persona.user', 'grupos.carrera'])
            ->withCount('grupos')
            ->orderByDesc('id')
            ->paginate(10);

        return view('admin.tutores.index', ['tutores' => $tutores, 'personasDisponibles' => app(ExistingTutorRegistrationService::class)->availablePeople()]);
    }

    public function create()
    {
        return redirect()->route('admin.tutores.index', ['crear' => 1]);
    }

    public function store(StoreTutorRequest $request)
    {
        if ($request->validated('tipo_registro') === 'existente') {
            app(ExistingTutorRegistrationService::class)->register($request->validated());
            Alert::success('Tutor vinculado', 'Se completó su expediente conservando su cuenta y sus otros roles.');
            return redirect()->route('admin.tutores.index');
        }
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
        $page = intdiv(Tutor::where('id', '>', $tutor->id)->count(), 10) + 1;
        return redirect()->route('admin.tutores.index', ['ver' => $tutor->id, 'page' => $page]);
    }

    public function edit(Tutor $tutor)
    {
        $page = intdiv(Tutor::where('id', '>', $tutor->id)->count(), 10) + 1;
        return redirect()->route('admin.tutores.index', ['editar' => $tutor->id, 'page' => $page]);
    }

    public function update(UpdateTutorRequest $request, Tutor $tutor)
    {
        $tutor->load(['persona.user', 'grupos.carrera']);

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

            if ($persona?->psicologo) {
                $user?->removeRole('tutor');
                return;
            }

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
