<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tutor extends Model
{
    use SoftDeletes;

    protected $table = 'tutores';

    protected $fillable = [
        'persona_id',
        'numero_empleado',
        'estado',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    // Relación directa legacy (retrocompatibilidad)
    public function grupos()
    {
        return $this->hasMany(Grupo::class);
    }

    // Relación pivote: grupos asignados por ciclo escolar
    public function gruposAsignados()
    {
        return $this->belongsToMany(Grupo::class, 'grupo_tutor')
            ->withPivot('ciclo_escolar_id', 'estado')
            ->withTimestamps();
    }
}
