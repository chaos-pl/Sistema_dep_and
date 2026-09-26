<?php

namespace App\Services;

use App\Models\Dass21Question;
use App\Models\Estudiante;
use App\Models\Instrumento;
use App\Models\QuestionnaireDraft;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuestionnaireDraftService
{
    public function read(Estudiante $student, string $instrument): array
    {
        $draft = QuestionnaireDraft::where('estudiante_id', $student->id)->where('instrument', strtoupper($instrument))->first();

        return ['draftAnswers' => $draft && ! $draft->completed ? $draft->answers : [], 'draftVersion' => $draft?->version ?? 0];
    }

    public function save(Estudiante $student, string $instrument, array $answers, int $version): QuestionnaireDraft
    {
        $ids = match ($instrument) {
            'DASS21' => Dass21Question::where('instrument_id', Instrumento::where('acronimo', 'DASS21')->value('id'))->pluck('id')->all(),
            'PHQ9' => range(1, 9), 'GAD7' => range(1, 7),
            default => [],
        };
        foreach ($answers as $key => $value) {
            if (! in_array((int) $key, $ids, true) || (string) (int) $key !== (string) $key
                || ! is_int($value) || $value < 0 || $value > 3) {
                throw ValidationException::withMessages(['answers' => 'El borrador contiene respuestas no válidas.']);
            }
        }

        return DB::transaction(function () use ($student, $instrument, $answers, $version) {
            $draft = $this->lock($student, $instrument);
            $this->checkVersion($draft, $version);
            $draft->fill(['answers' => $answers, 'completed' => false, 'version' => $draft->version + 1])->save();

            return $draft;
        });
    }

    private function lock(Estudiante $student, string $instrument): QuestionnaireDraft
    {
        Estudiante::whereKey($student->id)->lockForUpdate()->firstOrFail();

        return QuestionnaireDraft::firstOrCreate(['estudiante_id' => $student->id, 'instrument' => strtoupper($instrument)],
            ['answers' => [], 'version' => 0, 'completed' => false]);
    }

    private function checkVersion(QuestionnaireDraft $draft, int $version): void
    {
        if ($draft->version !== $version) {
            throw ValidationException::withMessages(['draft_version' => 'El cuestionario cambió en otra pestaña. Recarga antes de continuar.']);
        }
    }

    /** Dentro de la transacción de envío. Se conserva versión para rechazar guardados atrasados. */
    public function complete(Estudiante $student, string $instrument, ?int $version): void
    {
        $draft = $this->lock($student, $instrument);
        if ($version !== null) {
            $this->checkVersion($draft, $version);
        }
        $draft->update(['answers' => [], 'completed' => true, 'version' => $draft->version + 1]);
    }
}
