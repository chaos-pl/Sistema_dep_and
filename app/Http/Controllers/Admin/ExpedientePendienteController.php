<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CompletarExpedienteEstudianteRequest;
use App\Models\Estudiante;
use App\Models\Grupo;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class ExpedientePendienteController extends Controller
{
    public function index()
    {
        $usuarios = User::role('estudiante')
            ->with('persona')
            ->whereDoesntHave('persona.estudiante')
            ->latest()
            ->paginate(10);

        return view('admin.expedientes-pendientes.index', compact('usuarios'));
    }

    public function edit(User $user)
    {
        abort_unless($user->hasRole('estudiante'), 404, 'El usuario no es estudiante.');

        $user->load('persona');

        if (!$user->persona) {
            Alert::warning('Expediente incompleto', 'Este usuario no tiene persona vinculada. Primero crea su persona.');
            return redirect()->route('admin.expedientes-pendientes.index');
        }

        if ($user->persona->estudiante) {
            Alert::info('Ya completado', 'Este usuario ya tiene expediente de estudiante.');
            return redirect()->route('admin.expedientes-pendientes.index');
        }

        $grupos = Grupo::with(['carrera', 'tutor.persona'])
            ->orderBy('nombre')
            ->get();

        return view('admin.expedientes-pendientes.edit', compact('user', 'grupos'));
    }

    public function update(CompletarExpedienteEstudianteRequest $request, User $user)
    {
        abort_unless($user->hasRole('estudiante'), 404, 'El usuario no es estudiante.');

        $user->load('persona');

        if (!$user->persona) {
            Alert::warning('No permitido', 'Este usuario no tiene persona vinculada.');
            return redirect()->route('admin.expedientes-pendientes.index');
        }

        if ($user->persona->estudiante) {
            Alert::info('Ya completado', 'Este usuario ya tiene expediente de estudiante.');
            return redirect()->route('admin.expedientes-pendientes.index');
        }

        DB::transaction(function () use ($request, $user) {
            Estudiante::create([
                'persona_id' => $user->persona->id,
                'matricula' => $request->matricula,
                'grupo_id' => $request->grupo_id,
                'codigo_anonimo' => $this->generarCodigoAnonimo(),
            ]);
        });

        Alert::success('Expediente completado', 'El estudiante ya quedó asignado a un grupo y con expediente formal.');

        return redirect()->route('admin.expedientes-pendientes.index');
    }

    private function generarCodigoAnonimo(): string
    {
        do {
            $codigo = 'EST-' . strtoupper(Str::random(8));
        } while (Estudiante::where('codigo_anonimo', $codigo)->exists());

        return $codigo;
    }
}
