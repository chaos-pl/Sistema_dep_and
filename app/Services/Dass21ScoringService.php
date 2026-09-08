<?php

namespace App\Services;

use App\Models\Dass21Question;

class Dass21ScoringService
{
    /**
     * Calcula los puntajes y niveles de severidad del DASS-21.
     *
     * @param array $answers Arreglo de respuestas en formato [question_id => score]
     * @return array
     */
    public function score(array $answers): array
    {
        // Obtenemos las preguntas respondidas para saber su dimensión
        $questions = Dass21Question::whereIn('id', array_keys($answers))->get();

        $rawScores = [
            'depression' => 0,
            'anxiety'    => 0,
            'stress'     => 0,
        ];

        // 1. Suma simple por dimensión (Raw score: 0 - 21)
        foreach ($questions as $question) {
            $score = (int) $answers[$question->id];
            $rawScores[$question->dimension] += $score;
        }

        // 2. Multiplicador x2 para escalar a baremo oficial (Score final: 0 - 42)
        $scores = [
            'depression_raw'   => $rawScores['depression'],
            'depression_score' => $rawScores['depression'] * 2,
            'anxiety_raw'      => $rawScores['anxiety'],
            'anxiety_score'    => $rawScores['anxiety'] * 2,
            'stress_raw'       => $rawScores['stress'],
            'stress_score'     => $rawScores['stress'] * 2,
        ];

        // 3. Clasificación de severidad por dimensión
        $levels = [
            'depression_level' => $this->getDepressionLevel($scores['depression_score']),
            'anxiety_level'    => $this->getAnxietyLevel($scores['anxiety_score']),
            'stress_level'     => $this->getStressLevel($scores['stress_score']),
        ];

        // 4. Determinar nivel de severidad global máximo (Triaje)
        $maxSeverity = $this->calculateMaxSeverity([
            $levels['depression_level'],
            $levels['anxiety_level'],
            $levels['stress_level']
        ]);

        return array_merge($scores, $levels, ['max_severity_level' => $maxSeverity]);
    }

    private function getDepressionLevel(int $score): string
    {
        return match (true) {
            $score <= 9  => 'Normal',
            $score <= 13 => 'Leve',
            $score <= 20 => 'Moderado',
            $score <= 27 => 'Severo',
            default      => 'Extremadamente severo',
        };
    }

    private function getAnxietyLevel(int $score): string
    {
        return match (true) {
            $score <= 7  => 'Normal',
            $score <= 9  => 'Leve',
            $score <= 14 => 'Moderado',
            $score <= 19 => 'Severo',
            default      => 'Extremadamente severo',
        };
    }

    private function getStressLevel(int $score): string
    {
        return match (true) {
            $score <= 14 => 'Normal',
            $score <= 18 => 'Leve',
            $score <= 25 => 'Moderado',
            $score <= 33 => 'Severo',
            default      => 'Extremadamente severo',
        };
    }

    private function calculateMaxSeverity(array $levels): string
    {
        $hierarchy = [
            'Extremadamente severo' => 5,
            'Severo'                => 4,
            'Moderado'              => 3,
            'Leve'                  => 2,
            'Normal'                => 1,
        ];

        $highestWeight = 1;
        $highestLevel = 'Normal';

        foreach ($levels as $level) {
            if (isset($hierarchy[$level]) && $hierarchy[$level] > $highestWeight) {
                $highestWeight = $hierarchy[$level];
                $highestLevel = $level;
            }
        }

        return $highestLevel;
    }
}
