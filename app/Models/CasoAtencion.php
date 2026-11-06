<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CasoAtencion extends Model
{
    protected $table = 'casos_atencion';

    protected $guarded = ['id'];

    protected $attributes = ['estado' => 'pendiente', 'version' => 0];

    protected $casts = ['asignado_at' => 'datetime', 'cerrado_at' => 'datetime', 'version' => 'integer'];

    protected static function booted(): void
    {
        static::saving(function (self $case) {
            if (($case->evaluacion_id === null) === ($case->analisis_nlp_id === null)) {
                throw new \LogicException('Un caso debe tener exactamente un origen.');
            }
        });
    }

    public function evaluacion()
    {
        return $this->belongsTo(Evaluacion::class);
    }

    public function analisisNlp()
    {
        return $this->belongsTo(AnalisisNlp::class);
    }

    public function psicologo()
    {
        return $this->belongsTo(Psicologo::class);
    }

    public function seguimientos()
    {
        return $this->hasMany(SeguimientoCaso::class);
    }

    public function getOrigenTextoAttribute(): string
    {
        return $this->evaluacion_id ? ($this->evaluacion?->instrumento?->acronimo ?? 'Cuestionario') : 'Diario IA';
    }

    public function actividadClinica()
    {
        return $this->seguimientos()->whereIn('tipo', ['valoracion', 'nota']);
    }

    public function scopeBandeja($query, string $bandeja, Psicologo $psicologo)
    {
        return match ($bandeja) {
            'sin_asignar' => $query->whereNull('psicologo_id')->where('estado', '!=', 'cerrado'),
            'por_revisar' => $query->where('psicologo_id', $psicologo->id)->where('estado', '!=', 'cerrado')
                ->doesntHave('actividadClinica')->whereDoesntHave('evaluacion.diagnostico'),
            'seguimientos' => $query->where('psicologo_id', $psicologo->id)->where('estado', 'seguimiento'),
        };
    }

    public function getEstudianteAttribute()
    {
        return $this->evaluacion_id ? $this->evaluacion?->estudiante : $this->analisisNlp?->estudiante;
    }

    public function scopeVisibleTo($query, User $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->whereRaw('1 = 0');
            if ($user->can('evaluaciones.historial.global') && $user->can('evaluaciones.respuestas.detalle')) {
                $q->orWhereNotNull('evaluacion_id');
            }
            if ($user->can('resultados_ia.ver')) {
                $q->orWhereNotNull('analisis_nlp_id');
            }
        });
    }
}
