<?php

namespace App\Http\Controllers\ControlEscolar;

use App\Http\Controllers\Controller;
use App\Models\CicloEscolar;
use App\Models\Grupo;
use App\Models\GrupoTutor;
use App\Models\Tutor;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use RealRashid\SweetAlert\Facades\Alert;

class AsignacionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:tutores.asignar_grupo', only: ['index']),
            new Middleware('permission:tutores.asignar_grupo', only: ['store']),
            new Middleware('permission:tutores.asignar_grupo', only: ['destroy']),
        ];
    }

    public function index()
    {
        $asignaciones = GrupoTutor::with(['grupo.carrera', 'tutor.persona', 'cicloEscolar'])
            ->latest()
            ->paginate(15);

        $grupos = Grupo::where('estado', 'activo')->orderBy('nombre')->get();
        $tutores = Tutor::with('persona')->where('estado', 'activo')->get();
        $ciclos = CicloEscolar::where('estado', 'activo')->orderBy('nombre', 'desc')->get();

        return view('control_escolar.asignaciones.index', compact('asignaciones', 'grupos', 'tutores', 'ciclos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'grupo_id' => 'required|exists:grupos,id',
            'tutor_id' => 'required|exists:tutores,id',
            'ciclo_escolar_id' => 'nullable|exists:ciclos_escolares,id',
        ]);

        $grupo = Grupo::findOrFail($request->grupo_id);
        app(\App\Services\GroupTutorAssignmentService::class)->save($grupo, [
            'tutor_id' => $request->tutor_id,
            'ciclo_escolar_id' => $request->ciclo_escolar_id ?? $grupo->ciclo_escolar_id,
        ]);
        Alert::success('Asignación creada', 'El tutor fue asignado al grupo correctamente.');

        return redirect()->route('control_escolar.asignaciones.index');
    }

    public function destroy(GrupoTutor $asignacion)
    {
        app(\App\Services\GroupTutorAssignmentService::class)->remove($asignacion);

        Alert::success('Asignación eliminada', 'La asignación fue removida correctamente.');

        return redirect()->route('control_escolar.asignaciones.index');
    }
}
