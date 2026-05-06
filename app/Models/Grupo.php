<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grupo extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'carrera_id',
        'tutor_id',
        'ciclo_escolar_id',
        'nombre',
        'periodo',
        'estado',
    ];

    public function carrera()
    {
        return $this->belongsTo(Carrera::class);
    }

    // Relación directa legacy (retrocompatibilidad con módulo Tutor existente)
    public function tutor()
    {
        return $this->belongsTo(Tutor::class);
    }

    public function cicloEscolar()
    {
        return $this->belongsTo(CicloEscolar::class, 'ciclo_escolar_id');
    }

    public function estudiantes()
    {
        return $this->hasMany(Estudiante::class);
    }

    // Relación pivote: tutores asignados por ciclo escolar
    public function tutoresAsignados()
    {
        return $this->belongsToMany(Tutor::class, 'grupo_tutor')
            ->withPivot('ciclo_escolar_id', 'estado')
            ->withTimestamps();
    }
}
