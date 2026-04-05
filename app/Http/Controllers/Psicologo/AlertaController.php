<?php

namespace App\Http\Controllers\Psicologo;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use Illuminate\Support\Facades\Auth;

class AlertaController extends Controller
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

        $alertas = Alerta::with([
            'evaluacion.instrumento',
            'evaluacion.resultadoClinico',
            'evaluacion.estudiante.persona',
            'evaluacion.diagnostico',
        ])
            ->latest()
            ->paginate(10);

        $totalGeneradas = Alerta::where('estado', 'generada')->count();
        $totalAsignadas = Alerta::where('estado', 'asignada_psicologo')->count();
        $totalAtendidas = Alerta::where('estado', 'atendida')->count();

        return view('psicologo.alertas.index', compact(
            'alertas',
            'totalGeneradas',
            'totalAsignadas',
            'totalAtendidas'
        ));
    }

    public function show(Alerta $alerta)
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

        if ($alerta->estado === 'generada') {
            $alerta->update([
                'estado' => 'asignada_psicologo',
            ]);
        }

        $alerta->load([
            'evaluacion.instrumento',
            'evaluacion.resultadoClinico',
            'evaluacion.respuestas',
            'evaluacion.estudiante.persona.user',
            'evaluacion.diagnostico',
        ]);

        return view('psicologo.alertas.show', compact('alerta'));
    }
}
