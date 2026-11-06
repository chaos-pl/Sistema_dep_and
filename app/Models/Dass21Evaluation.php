<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dass21Evaluation extends Model
{
    protected $table = 'dass21_evaluations';

    protected $fillable = [
        'evaluacion_id',
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
            'depression_raw' => 'integer',
            'depression_score' => 'integer',
            'anxiety_raw' => 'integer',
            'anxiety_score' => 'integer',
            'stress_raw' => 'integer',
            'stress_score' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Relaciones */
    /* ------------------------------------------------------------------ */

    public function instrumento(): BelongsTo
    {
        return $this->belongsTo(Instrumento::class, 'instrument_id');
    }

    public function evaluacion(): BelongsTo
    {
        return $this->belongsTo(Evaluacion::class);
    }

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class, 'codigo_anonimo', 'codigo_anonimo');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Dass21EvaluationAnswer::class, 'evaluation_id');
    }

    /* ------------------------------------------------------------------ */
    /*  Helpers */
    /* ------------------------------------------------------------------ */

    /**
     * Retorna true si alguna dimensión es "Severo" o "Extremadamente severo".
     */
    public function hasCriticalSeverity(): bool
    {
        return in_array($this->max_severity_level, ['Severo', 'Extremadamente severo'], true);
    }

    public function scopeUltimoSeveroPorEstudiante($query)
    {
        return $query->ultimoPorEstudiante()->whereIn('max_severity_level', ['Severo', 'Extremadamente severo']);
    }

    public function scopeUltimoPorEstudiante($query)
    {
        return $query->whereHas('estudiante')
            ->where('id', '=', function ($latest) {
                $latest->from('dass21_evaluations as aplicaciones')->select('aplicaciones.id')
                    ->whereColumn('aplicaciones.codigo_anonimo', 'dass21_evaluations.codigo_anonimo')
                    ->whereNotNull('aplicaciones.completed_at')
                    ->orderByDesc('aplicaciones.completed_at')->orderByDesc('aplicaciones.id')->limit(1);
            });
    }
}
