<?php

namespace App\Services;

use App\Models\Alerta;
use App\Models\Dass21Evaluation;
use App\Models\Dass21Question;
use App\Models\Estudiante;
use App\Models\Evaluacion;
use App\Models\Instrumento;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class Dass21EvaluationService
{
    public function __construct(private Dass21ScoringService $scoring) {}

    public function submit(Estudiante $estudiante, array $answers, ?int $draftVersion = null): Dass21Evaluation
    {
        return DB::transaction(function () use ($estudiante, $answers, $draftVersion) {
            app(QuestionnaireDraftService::class)->complete($estudiante, 'DASS21', $draftVersion);
            $instrumento = Instrumento::where('acronimo', 'DASS21')->firstOrFail();
            $questions = Dass21Question::where('instrument_id', $instrumento->id)->get();
            $ids = $questions->modelKeys();
            $submitted = array_keys($answers);
            sort($ids);
            sort($submitted);
            $validScores = collect($answers)->every(fn ($score) => filter_var($score, FILTER_VALIDATE_INT) !== false && $score >= 0 && $score <= 3);
            if (count($ids) !== 21 || $ids !== $submitted || ! $validScores
                || $questions->where('dimension', 'depression')->count() !== 7
                || $questions->where('dimension', 'anxiety')->count() !== 7
                || $questions->where('dimension', 'stress')->count() !== 7) {
                throw ValidationException::withMessages(['answers' => 'Responde las 21 preguntas vigentes de DASS-21 con valores de 0 a 3.']);
            }

            $evaluation = Dass21Evaluation::create(array_merge($this->scoring->score($answers), [
                'codigo_anonimo' => $estudiante->codigo_anonimo,
                'instrument_id' => $instrumento->id,
                'completed_at' => now(),
            ]));
            foreach ($answers as $questionId => $score) {
                $evaluation->answers()->create(['question_id' => $questionId, 'score' => (int) $score]);
            }
            $general = $this->link($evaluation);
            app(EvaluationContextService::class)->capture($general, $estudiante);
            // Conserva el criterio de severidad ya definido en el modelo DASS-21.
            if ($evaluation->hasCriticalSeverity()) {
                Alerta::firstOrCreate(['evaluacion_id' => $general->id], ['estado' => 'generada']);
            }

            return $evaluation->refresh();
        });
    }

    /** Vincula históricos sin recalcular respuestas ni generar alertas retrospectivas. */
    public function link(Dass21Evaluation $evaluation): Evaluacion
    {
        return DB::transaction(function () use ($evaluation) {
            $locked = Dass21Evaluation::whereKey($evaluation->id)->lockForUpdate()->firstOrFail();
            if ($locked->evaluacion_id) {
                return $locked->evaluacion()->firstOrFail();
            }
            $date = $locked->completed_at ?? $locked->created_at;
            $general = Evaluacion::create([
                'codigo_anonimo' => $locked->codigo_anonimo,
                'instrumento_id' => $locked->instrument_id,
                'estado' => 'completada',
            ]);
            $general->forceFill(['created_at' => $date, 'updated_at' => $locked->updated_at ?? $date])->save();
            // Mantener las marcas de tiempo originales del detalle.
            Dass21Evaluation::withoutTimestamps(fn () => $locked->update(['evaluacion_id' => $general->id]));

            return $general;
        });
    }
}
