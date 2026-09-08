<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDass21Request;
use App\Models\Dass21Evaluation;
use App\Models\Dass21EvaluationAnswer;
use App\Models\Dass21Question;
use App\Models\Instrumento;
use App\Services\Dass21ScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class Dass21Controller extends Controller
{
    public function create()
    {
        $user = Auth::user()->load('persona.estudiante');
        $estudiante = $user->persona?->estudiante;

        if (!$estudiante) {
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

    public function store(StoreDass21Request $request, Dass21ScoringService $scoringService)
    {
        $user = Auth::user()->load('persona.estudiante');
        $estudiante = $user->persona?->estudiante;

        if (!$estudiante) {
            return redirect()->route('evaluaciones.index');
        }

        $validated = $request->validated();
        $answers = $validated['answers'];

        $evaluation = DB::transaction(function () use ($answers, $scoringService, $estudiante) {

            $instrument = Instrumento::where('acronimo', 'DASS21')->firstOrFail();

            $scores = $scoringService->score($answers);

            // Guardamos usando el código anónimo
            $evaluationData = array_merge([
                'codigo_anonimo' => $estudiante->codigo_anonimo,
                'instrument_id'  => $instrument->id,
                'completed_at'   => now(),
            ], $scores);

            $evaluation = Dass21Evaluation::create($evaluationData);

            foreach ($answers as $questionId => $score) {
                Dass21EvaluationAnswer::create([
                    'evaluation_id' => $evaluation->id,
                    'question_id'   => $questionId,
                    'score'         => (int) $score,
                ]);
            }

            // TODO: Integrar alerta clínica (ej. Alerta::firstOrCreate(...)) si es necesario en el futuro.

            return $evaluation;
        });

        Alert::success('Tamizaje completado', 'Tus resultados DASS-21 han sido procesados.');

        return redirect()->route('dass21.show', $evaluation->id);
    }

    public function show(Dass21Evaluation $evaluation)
    {
        $user = Auth::user()->load('persona.estudiante');
        $estudiante = $user->persona?->estudiante;

        // Validamos que el estudiante logueado sea el dueño de la evaluación a través del código anónimo
        if (!$estudiante || $estudiante->codigo_anonimo !== $evaluation->codigo_anonimo) {
            abort(403, 'No tienes permiso para visualizar estos resultados.');
        }

        $evaluation->load('answers.question');

        return view('dass21.show', compact('evaluation'));
    }
}
