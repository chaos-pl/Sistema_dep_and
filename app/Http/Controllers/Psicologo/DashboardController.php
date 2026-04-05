<?php

namespace App\Http\Controllers\Psicologo;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
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

        if (!$persona || !$psicologo) {
            return view('psicologo.pendiente-expediente', [
                'titulo' => 'Expediente profesional pendiente',
                'mensaje' => 'Tu cuenta tiene el rol de psicólogo, pero todavía no se ha completado tu expediente clínico en el sistema.',
                'estado' => 'sin_expediente',
            ]);
        }

        $totalAlertasPendientes = Alerta::whereIn('estado', ['generada', 'asignada_psicologo'])->count();
        $totalAlertasAtendidas = Alerta::where('estado', 'atendida')->count();
        $totalDiagnosticosPropios = Diagnostico::where('psicologo_id', $psicologo->id)->count();
        $totalCasosSeveros = ResultadoClinico::where('nivel_riesgo', 'severo')->count();

        $alertas = Alerta::with([
            'evaluacion.instrumento',
            'evaluacion.resultadoClinico',
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

        return view('psicologo.dashboard', compact(
            'psicologo',
            'alertas',
            'resultados',
            'totalAlertasPendientes',
            'totalAlertasAtendidas',
            'totalDiagnosticosPropios',
            'totalCasosSeveros'
        ));
    }
}
