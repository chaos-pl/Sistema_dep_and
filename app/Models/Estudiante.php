<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estudiante extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'persona_id',
        'matricula',
        'grupo_id',
        'codigo_anonimo',
        'estado',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }

    public function evaluaciones()
    {
        return $this->hasMany(Evaluacion::class, 'codigo_anonimo', 'codigo_anonimo');
    }

    public function scopeConSeguimientoPendiente($query)
    {
        return $query->where('estado', 'activo')->where(function ($q) {
            $q->whereHas('evaluaciones', fn ($evaluations) => $evaluations->where(function ($e) {
                $e->whereHas('casoAtencion', fn ($c) => $c->where('estado', '!=', 'cerrado'))
                    ->orWhere(fn ($legacy) => $legacy->doesntHave('casoAtencion')
                        ->whereHas('alerta', fn ($a) => $a->whereIn('estado', ['generada', 'asignada_psicologo'])));
            }))->orWhereHas('analisisNlp.casoAtencion', fn ($c) => $c->where('estado', '!=', 'cerrado'));
        });
    }

    public function dass21Evaluations()
    {
        return $this->hasMany(Dass21Evaluation::class, 'codigo_anonimo', 'codigo_anonimo');
    }

    public function latestDass21()
    {
        return $this->hasOne(Dass21Evaluation::class, 'codigo_anonimo', 'codigo_anonimo')
            ->ofMany(['completed_at' => 'max', 'id' => 'max']);
    }

    public function analisisNlp()
    {
        return $this->hasMany(AnalisisNlp::class, 'codigo_anonimo', 'codigo_anonimo');
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoEstudiante::class);
    }
}
