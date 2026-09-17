<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalisisNlp extends Model
{
    public function casoAtencion()
    {
        return $this->hasOne(CasoAtencion::class);
    }

    protected $table = 'analisis_nlp';

    protected $fillable = [
        'codigo_anonimo',
        'texto_ingresado',
        'etiqueta_roberta',
        'score_confianza',
        'requiere_atencion',
        'estado_analisis', 'solicitud_id', 'solicitado_at', 'procesado_at', 'error_codigo',
        'probabilidad_beto', 'etiqueta_hibrida', 'confianza_hibrida',
    ];

    protected $casts = [
        'score_confianza' => 'decimal:4',
        'requiere_atencion' => 'boolean',
        'solicitado_at' => 'datetime',
        'procesado_at' => 'datetime',
        'probabilidad_beto' => 'decimal:4',
        'confianza_hibrida' => 'decimal:4',
    ];

    public function getEstadoTextoAttribute(): string
    {
        return match ($this->estado_analisis) {
            'pendiente' => 'En cola',
            'procesando' => 'Procesando',
            'completado' => 'Completado',
            'fallido' => 'Análisis no disponible',
            default => 'Resultado anterior no verificado',
        };
    }

    public function getAtencionTextoAttribute(): string
    {
        if ($this->requiere_atencion) {
            return 'Señal de atención para revisión profesional';
        }

        return $this->estado_analisis === 'completado'
            ? 'Sin señal automática de atención'
            : 'Sin resultado actualizado; no indica ausencia de riesgo';
    }

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'codigo_anonimo', 'codigo_anonimo');
    }
}
