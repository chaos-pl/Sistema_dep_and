<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    protected $fillable = [
        'persona_id',
        'matricula',
        'grupo_id',
        'codigo_anonimo',
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

    public function analisisNlp()
    {
        return $this->hasMany(AnalisisNlp::class, 'codigo_anonimo', 'codigo_anonimo');
    }
}
