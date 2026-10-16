<?php

namespace App\Http\Controllers;

use App\Models\Dass21Evaluation;
use App\Models\Estudiante;
use App\Models\Evaluacion;
use App\Services\Dass21CoverageService;
use Illuminate\Http\Request;

class EvolucionController extends Controller
{
    public function own(Request $request)
    {
        $user = $request->user();

        abort_unless(
            $user->hasRole('estudiante')
            && $user->can('evaluaciones.historial.propio'),
            403
        );

        $student = $user->persona?->estudiante;

        if (! $student) {
            \RealRashid\SweetAlert\Facades\Alert::info(
                'Expediente pendiente',
                'Tu cuenta fue registrada correctamente. '
                .'Mi evolución estará disponible cuando administración '
                .'o control escolar complete tu expediente de estudiante.'
            );

            return redirect()->route('estudiante.dashboard');
        }

        return $this->render($request, $student);
    }

    public function show(Request $request, Estudiante $estudiante)
    {
        abort_unless($request->user()->hasRole('psicologo') && $request->user()->persona?->psicologo
            && $request->user()->can('evaluaciones.historial.global') && $request->user()->can('evaluaciones.respuestas.detalle'), 403);

        return $this->render($request, $estudiante);
    }

    private function render(Request $request, Estudiante $student)
    {
        $request->mergeIfMissing(['desde' => now()->subYear()->toDateString()]);
        [$from, $to] = app(Dass21CoverageService::class)->period($request);
        $dass = Dass21Evaluation::where('codigo_anonimo', $student->codigo_anonimo)
            ->whereRaw('COALESCE(completed_at, created_at) BETWEEN ? AND ?', [$from, $to])
            ->orderByRaw('COALESCE(completed_at, created_at)')->orderBy('id')->get();
        $series = [];
        foreach (['depression' => 'Depresión', 'anxiety' => 'Ansiedad', 'stress' => 'Estrés'] as $key => $label) {
            $series[] = ['name' => 'DASS-21 · '.$label, 'max' => 42,
                'points' => $dass->map(fn ($d) => ['date' => $d->completed_at ?? $d->created_at,
                    'score' => $d->{$key.'_score'}, 'level' => $d->{$key.'_level'}])->all()];
        }
        $evaluations = Evaluacion::with(['instrumento', 'resultadoClinico'])->where('codigo_anonimo', $student->codigo_anonimo)
            ->where('estado', 'completada')->whereBetween('created_at', [$from, $to])->orderBy('created_at')->orderBy('id')->get();
        foreach (['PHQ9' => 27, 'GAD7' => 21] as $name => $max) {
            $series[] = ['name' => $name, 'max' => $max, 'points' => $evaluations
                ->filter(fn ($e) => strtoupper($e->instrumento->acronimo) === $name && $e->resultadoClinico)
                ->map(fn ($e) => ['date' => $e->created_at, 'score' => $e->resultadoClinico->puntaje_total,
                    'level' => $e->resultadoClinico->nivel_riesgo])->values()->all()];
        }

        return view('evolucion.index', compact('series', 'student', 'from', 'to'));
    }
}
