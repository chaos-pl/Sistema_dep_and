<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluacion extends Model
{
    public function casoAtencion()
    {
        return $this->hasOne(CasoAtencion::class);
    }

    protected $table = 'evaluaciones';

    protected $fillable = [
        'codigo_anonimo',
        'instrumento_id',
        'estado',
    ];

    public function instrumento()
    {
        return $this->belongsTo(Instrumento::class, 'instrumento_id');
    }

    public function dass21()
    {
        return $this->hasOne(Dass21Evaluation::class, 'evaluacion_id');
    }

    public function getNivelResumenAttribute(): string
    {
        return $this->dass21?->max_severity_level ?? $this->resultadoClinico?->nivel_riesgo ?? 'Sin resultado';
    }

    public function getPuntajeResumenAttribute(): string
    {
        if ($dass = $this->dass21) {
            return "D: {$dass->depression_score} · A: {$dass->anxiety_score} · E: {$dass->stress_score}";
        }

        return (string) ($this->resultadoClinico?->puntaje_total ?? 'N/D');
    }

    public function respuestas()
    {
        return $this->hasMany(Respuesta::class, 'evaluacion_id');
    }

    public function resultadoClinico()
    {
        return $this->hasOne(ResultadoClinico::class, 'evaluacion_id');
    }

    public function alerta()
    {
        return $this->hasOne(Alerta::class, 'evaluacion_id');
    }

    public function diagnostico()
    {
        return $this->hasOne(Diagnostico::class, 'evaluacion_id');
    }

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'codigo_anonimo', 'codigo_anonimo');
    }
}
