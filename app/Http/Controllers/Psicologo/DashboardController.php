<?php

namespace App\Http\Controllers\Psicologo;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use App\Models\CasoAtencion;
use App\Models\AnalisisNlp;
use App\Models\Dass21Evaluation;
use App\Models\Diagnostico;
use App\Models\ResultadoClinico;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('persona.psicologo');

        $persona = $user->persona;
        $psicologo = $persona?->psicologo;

        if (! $persona || ! $psicologo) {
            return view('psicologo.pendiente-expediente', [
                'titulo' => 'Expediente profesional pendiente',
                'mensaje' => 'Tu cuenta tiene el rol de psicólogo, pero todavía no se ha completado tu expediente clínico en el sistema.',
                'estado' => 'sin_expediente',
            ]);
        }

        $totalAlertasPendientes = Alerta::whereIn('estado', ['generada', 'asignada_psicologo'])->count();
        $totalAlertasAtendidas = Alerta::where('estado', 'atendida')->count();
        $totalDiagnosticosPropios = Diagnostico::where('psicologo_id', $psicologo->id)->count();

        $casos = CasoAtencion::visibleTo($user);
        $sinAsignar = (clone $casos)->bandeja('sin_asignar', $psicologo)
            ->selectRaw('COUNT(*) as total, MIN(created_at) as primero')->first();
        $porRevisar = (clone $casos)->bandeja('por_revisar', $psicologo)->count();
        $misSeguimientos = (clone $casos)->bandeja('seguimientos', $psicologo)->count();
        $casosPorEstado = (clone $casos)->where('psicologo_id', $psicologo->id)
            ->selectRaw('estado, COUNT(*) as total')->groupBy('estado')->pluck('total', 'estado');
        $nivelesDass = $user->can('evaluaciones.historial.global')
            ? Dass21Evaluation::ultimoPorEstudiante()->selectRaw('max_severity_level, COUNT(*) as total')
                ->groupBy('max_severity_level')->pluck('total', 'max_severity_level')
            : collect();
        $estudiantesSeveros = $nivelesDass->get('Severo', 0) + $nivelesDass->get('Extremadamente severo', 0);

        $dass21Recientes = Dass21Evaluation::with(['estudiante.persona', 'evaluacion.alerta'])
            ->latest('completed_at')->latest('id')->take(6)->get();

        $totalResultadosIaRiesgo = AnalisisNlp::where('requiere_atencion', true)->count();

        $alertas = Alerta::with([
            'evaluacion.instrumento',
            'evaluacion.resultadoClinico',
            'evaluacion.dass21',
            'evaluacion.estudiante.persona',
        ])
            ->latest()
            ->take(6)
            ->get();

        $resultados = ResultadoClinico::with([
            'evaluacion.instrumento',
            'evaluacion.estudiante.persona',
        ])
            ->whereIn('nivel_riesgo', ['moderado', 'severo'])
            ->latest()
            ->take(6)
            ->get();

        $analisisNlp = AnalisisNlp::with([
            'estudiante.persona',
        ])
            ->where('requiere_atencion', true)
            ->latest()
            ->take(6)
            ->get();

        return view('psicologo.dashboard', compact(
            'psicologo',
            'dass21Recientes',
            'alertas',
            'resultados',
            'analisisNlp',
            'totalAlertasPendientes',
            'totalAlertasAtendidas',
            'totalDiagnosticosPropios',
            'sinAsignar', 'porRevisar', 'misSeguimientos', 'estudiantesSeveros',
            'casosPorEstado', 'nivelesDass',
            'totalResultadosIaRiesgo'
        ));
    }
}
