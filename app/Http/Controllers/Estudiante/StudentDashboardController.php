<?php

namespace App\Http\Controllers\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\AnalisisNlp;
use App\Models\Evaluacion;
use App\Models\Instrumento;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load([
            'persona.estudiante.grupo',
        ]);

        $persona = $user->persona;
        $estudiante = $persona?->estudiante;

        // Caso 1: el usuario todavía no tiene persona vinculada
        if (!$persona) {
            return view('estudiante.pendiente-expediente', [
                'titulo' => 'Expediente pendiente',
                'mensaje' => 'Tu cuenta aún no tiene una persona vinculada. Espera a que administración complete tu registro.',
                'estado' => 'sin_persona',
            ]);
        }

        // Caso 2: sí tiene persona, pero aún no tiene expediente de estudiante
        if (!$estudiante) {
            return view('estudiante.pendiente-expediente', [
                'titulo' => 'Asignación de grupo pendiente',
                'mensaje' => 'Tu registro fue creado correctamente, pero todavía no se te ha asignado un grupo ni completado tu expediente académico.',
                'estado' => 'sin_expediente',
            ]);
        }

        $instrumentos = Instrumento::whereIn('acronimo', ['PHQ9', 'GAD7', 'phq9', 'gad7'])
            ->orderBy('nombre')
            ->get();

        $evaluaciones = Evaluacion::with(['instrumento', 'resultadoClinico'])
            ->where('codigo_anonimo', $estudiante->codigo_anonimo)
            ->latest()
            ->get();

        $instrumentosDashboard = $instrumentos->map(function ($instrumento) use ($evaluaciones) {
            $ultima = $evaluaciones
                ->where('instrumento_id', $instrumento->id)
                ->sortByDesc('id')
                ->first();

            return (object) [
                'instrumento' => $instrumento,
                'evaluacion' => $ultima,
                'estado' => $ultima?->estado ?? 'pendiente',
                'puntaje_total' => $ultima?->resultadoClinico?->puntaje_total,
                'nivel_riesgo' => $ultima?->resultadoClinico?->nivel_riesgo,
            ];
        });

        $totalCompletadas = $evaluaciones
            ->where('estado', 'completada')
            ->count();

        $ultimaEntrada = AnalisisNlp::where('codigo_anonimo', $estudiante->codigo_anonimo)
            ->latest()
            ->first();

        return view('estudiante.dashboard', compact(
            'user',
            'estudiante',
            'instrumentosDashboard',
            'totalCompletadas',
            'ultimaEntrada'
        ));
    }
}
