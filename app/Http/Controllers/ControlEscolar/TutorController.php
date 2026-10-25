<?php

namespace App\Http\Controllers\ControlEscolar;

use App\Http\Controllers\Controller;
use App\Services\ExistingTutorRegistrationService;
use App\Models\Persona;
use App\Http\Requests\Admin\StoreTutorRequest;
use App\Http\Requests\Admin\UpdateTutorRequest;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class TutorController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:tutores.ver', only: ['index', 'show']),
            new Middleware('permission:tutores.crear', only: ['create']),
            new Middleware('permission:tutores.crear', only: ['store']),
            new Middleware('permission:tutores.editar', only: ['edit']),
            new Middleware('permission:tutores.editar', only: ['update']),
            new Middleware('permission:usuarios.eliminar', only: ['destroy']),
        ];
    }

    public function index()
    {
        $tutores = Tutor::with(['persona.user', 'grupos.carrera'])
            ->withCount('grupos')
            ->orderByDesc('id')
            ->paginate(10);

        return view('control_escolar.tutores.index', ['tutores' => $tutores, 'personasDisponibles' => app(ExistingTutorRegistrationService::class)->availablePeople()]);
    }

    public function create()
    {
        return redirect()->route('control_escolar.tutores.index', ['crear' => 1]);
    }

    public function store(StoreTutorRequest $request)
    {
        if ($request->validated('tipo_registro') === 'existente') {
            app(ExistingTutorRegistrationService::class)->register($request->validated());
            Alert::success('Tutor vinculado', 'Se completó su expediente conservando su cuenta y sus otros roles.');
            return redirect()->route('control_escolar.tutores.index');
        }
        DB::transaction(function () use ($request) {
            $nombreCompleto = trim($request->nombre.' '.$request->apellido_paterno.' '.($request->apellido_materno ?? ''));

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

        return redirect()->route('control_escolar.tutores.index');
    }

    public function show(Tutor $tutor)
    {
        $page = intdiv(Tutor::where('id', '>', $tutor->id)->count(), 10) + 1;
        return redirect()->route('control_escolar.tutores.index', ['ver' => $tutor->id, 'page' => $page]);
    }

    public function edit(Tutor $tutor)
    {
        $page = intdiv(Tutor::where('id', '>', $tutor->id)->count(), 10) + 1;
        return redirect()->route('control_escolar.tutores.index', ['editar' => $tutor->id, 'page' => $page]);
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

        return redirect()->route('control_escolar.tutores.index');
    }

    public function destroy(Tutor $tutor)
    {
        $tutor->loadCount('grupos');

        if ($tutor->grupos_count > 0) {
            Alert::warning('No permitido', 'No puedes dar de baja un tutor que tiene grupos asignados.');

            return redirect()->route('control_escolar.tutores.index');
        }

        DB::transaction(function () use ($tutor) {
            $tutor->delete(); // soft-delete
            if ($tutor->persona?->psicologo) {
                $tutor->persona->user?->removeRole('tutor');
            }
        });

        Alert::success('Tutor dado de baja', 'El tutor fue dado de baja del sistema.');

        return redirect()->route('control_escolar.tutores.index');
    }
}
