<?php

namespace App\Http\Controllers\Psicologo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Psicologo\StoreDiagnosticoRequest;
use App\Models\Diagnostico;
use App\Models\Evaluacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class DiagnosticoController extends Controller
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

        $diagnosticos = Diagnostico::with([
            'evaluacion.instrumento',
            'evaluacion.resultadoClinico',
            'evaluacion.estudiante.persona',
        ])
            ->where('psicologo_id', $psicologo->id)
            ->latest()
            ->paginate(10);

        return view('psicologo.diagnosticos.index', compact('diagnosticos'));
    }

    public function store(StoreDiagnosticoRequest $request)
    {
        $user = Auth::user()->load('persona.psicologo');

        $persona = $user->persona;
        $psicologo = $persona?->psicologo;

        if (!$persona || !$psicologo) {
            Alert::warning('Expediente pendiente', 'Tu cuenta todavía no tiene expediente profesional completo.');
            return redirect()->route('psicologo.dashboard');
        }

        $evaluacion = Evaluacion::with(['alerta', 'diagnostico'])->findOrFail($request->evaluacion_id);

        if ($evaluacion->diagnostico) {
            Alert::warning('Diagnóstico existente', 'Esta evaluación ya tiene un diagnóstico registrado.');
            return redirect()->route('alertas.show', $evaluacion->alerta?->id ?? 0);
        }

        DB::transaction(function () use ($request, $evaluacion, $psicologo) {
            Diagnostico::create([
                'evaluacion_id' => $evaluacion->id,
                'psicologo_id' => $psicologo->id,
                'impresion_diagnostica' => $request->impresion_diagnostica,
                'retroalimentacion_estudiante' => $request->retroalimentacion_estudiante,
                'requiere_derivacion' => $request->boolean('requiere_derivacion'),
            ]);

            if ($evaluacion->alerta) {
                $evaluacion->alerta->update([
                    'estado' => 'atendida',
                ]);
            }
        });

        Alert::success('Diagnóstico registrado', 'El diagnóstico fue guardado correctamente.');

        return redirect()->route('diagnosticos.index');
    }
}
