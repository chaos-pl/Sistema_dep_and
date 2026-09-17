<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use App\Models\Grupo;
use App\Services\Dass21CoverageService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, Dass21CoverageService $coverage)
    {
        $tutor = $request->user()->persona?->tutor;
        abort_unless($tutor, 403, 'No existe un tutor vinculado a este usuario.');
        $periodo = $coverage->period($request);
        $grupos = $coverage->groups(Grupo::visibleToTutor($tutor), $periodo);
        $grupos->each(fn ($grupo) => $grupo->estudiantes_count = $grupo->participantes);
        $totalGrupos = $grupos->count();
        $totalEstudiantes = $grupos->sum('participantes');
        $completadas = $grupos->sum('completadas');
        $pendientes = $grupos->sum('pendientes');
        // Pendientes vigentes, sin ocultarlos por el filtro de fechas de cobertura.
        $alumnosRiesgo = $request->user()->can('alertas.ver.general')
            ? Estudiante::conSeguimientoPendiente()->whereIn('grupo_id', $grupos->modelKeys())->count()
            : 0;

        return view('tutor.dashboard', compact('totalGrupos', 'totalEstudiantes', 'completadas', 'pendientes', 'grupos', 'periodo', 'alumnosRiesgo'));
    }
}
