<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDass21Request;
use App\Models\Dass21Evaluation;
use App\Models\Dass21Question;
use App\Services\Dass21EvaluationService;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class Dass21Controller extends Controller
{
    public function history()
    {
        $estudiante = Auth::user()->persona?->estudiante;
        abort_unless($estudiante, 403);
        $evaluaciones = $estudiante->dass21Evaluations()->latest('completed_at')->latest('id')->paginate(15);

        return view('dass21.history', compact('evaluaciones'));
    }

    public function create()
    {
        $user = Auth::user()->load('persona.estudiante');
        $estudiante = $user->persona?->estudiante;

        if (! $estudiante) {
            Alert::warning('Expediente pendiente', 'Tu expediente aún no está completo.');

            return redirect()->route('evaluaciones.index');
        }

        $questions = Dass21Question::orderBy('item_number')->get();

        // Validamos si ya hizo el test en los últimos 14 días usando el código anónimo
        $lastEvaluation = Dass21Evaluation::where('codigo_anonimo', $estudiante->codigo_anonimo)
            ->latest('completed_at')
            ->first();

        $hasRecentEvaluation = $lastEvaluation && $lastEvaluation->completed_at->diffInDays(now()) < 14;

        return view('dass21.create', compact('questions', 'hasRecentEvaluation', 'estudiante'));
    }

    public function store(StoreDass21Request $request, Dass21EvaluationService $service)
    {
        $user = Auth::user()->load('persona.estudiante');
        $estudiante = $user->persona?->estudiante;

        if (! $estudiante) {
            return redirect()->route('evaluaciones.index');
        }

        $evaluation = $service->submit($estudiante, $request->validated('answers'));

        Alert::success('Tamizaje completado', 'Tus resultados DASS-21 han sido procesados.');

        return redirect()->route('dass21.show', $evaluation->id);
    }

    public function show(Dass21Evaluation $evaluation)
    {
        $user = Auth::user()->load('persona.estudiante');
        $estudiante = $user->persona?->estudiante;

        // Validamos que el estudiante logueado sea el dueño de la evaluación a través del código anónimo
        if (! $estudiante || $estudiante->codigo_anonimo !== $evaluation->codigo_anonimo) {
            abort(403, 'No tienes permiso para visualizar estos resultados.');
        }

        $evaluation->load(['answers.question', 'evaluacion.diagnostico']);

        return view('dass21.show', compact('evaluation'));
    }
}
