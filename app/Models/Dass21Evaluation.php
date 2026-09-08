<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dass21Evaluation extends Model
{
    protected $table = 'dass21_evaluations';

    protected $fillable = [
        'codigo_anonimo',
        'instrument_id',
        'depression_raw',
        'depression_score',
        'depression_level',
        'anxiety_raw',
        'anxiety_score',
        'anxiety_level',
        'stress_raw',
        'stress_score',
        'stress_level',
        'max_severity_level',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'depression_raw'   => 'integer',
            'depression_score' => 'integer',
            'anxiety_raw'      => 'integer',
            'anxiety_score'    => 'integer',
            'stress_raw'       => 'integer',
            'stress_score'     => 'integer',
            'completed_at'     => 'datetime',
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Relaciones                                                         */
    /* ------------------------------------------------------------------ */


    public function instrumento(): BelongsTo
    {
        return $this->belongsTo(Instrumento::class, 'instrument_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Dass21EvaluationAnswer::class, 'evaluation_id');
    }

    /* ------------------------------------------------------------------ */
    /*  Helpers                                                            */
    /* ------------------------------------------------------------------ */

    /**
     * Retorna true si alguna dimensión es "Severo" o "Extremadamente severo".
     */
    public function hasCriticalSeverity(): bool
    {
        return in_array($this->max_severity_level, ['Severo', 'Extremadamente severo'], true);
    }
}
