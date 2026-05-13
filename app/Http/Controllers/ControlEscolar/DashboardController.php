<?php

namespace App\Http\Controllers\ControlEscolar;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\CicloEscolar;
use App\Models\Estudiante;
use App\Models\Grupo;
use App\Models\MovimientoEstudiante;
use App\Models\Tutor;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // Métricas principales
        $totalEstudiantes = Estudiante::count();
        $estudiantesSinGrupo = Estudiante::whereNull('grupo_id')->count();
        $estudiantesActivos = Estudiante::where('estado', 'activo')->count();
        $totalGrupos = Grupo::count();
        $gruposActivos = Grupo::where('estado', 'activo')->count();
        $totalCarreras = Carrera::count();
        $carrerasActivas = Carrera::where('estado', 'activo')->count();
        $totalTutores = Tutor::count();
        $ciclosActivos = CicloEscolar::where('estado', 'activo')->count();

        // Consultas corregidas: incluir users sin expediente
        $sinExpediente = User::role('estudiante')
            ->where(function ($q) {
                $q->whereDoesntHave('persona')
                  ->orWhereDoesntHave('persona.estudiante');
            })
            ->count();

        $tutoresSinGrupo = Tutor::doesntHave('grupos')->count();

        // Grupos con conteo de estudiantes para la sección de detalles
        $gruposConEstudiantes = Grupo::with('carrera')
            ->withCount('estudiantes')
            ->where('estado', 'activo')
            ->orderByDesc('estudiantes_count')
            ->take(6)
            ->get();

        // Últimos movimientos para la sección de actividad reciente
        $ultimosMovimientos = MovimientoEstudiante::with([
                'estudiante.persona',
                'grupoOrigen',
                'grupoDestino',
                'realizadoPor',
            ])
            ->latest()
            ->take(5)
            ->get();

        // Pendientes total (sin expediente + sin grupo)
        $totalPendientes = $sinExpediente + $estudiantesSinGrupo;

        return view('control_escolar.dashboard', compact(
            'totalEstudiantes',
            'estudiantesSinGrupo',
            'estudiantesActivos',
            'totalGrupos',
            'gruposActivos',
            'totalCarreras',
            'carrerasActivas',
            'totalTutores',
            'ciclosActivos',
            'sinExpediente',
            'tutoresSinGrupo',
            'totalPendientes',
            'gruposConEstudiantes',
            'ultimosMovimientos',
        ));
    }
}
