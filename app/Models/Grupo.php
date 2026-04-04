<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $fillable = [
        'carrera_id',
        'tutor_id',
        'nombre',
        'periodo',
    ];

    public function carrera()
    {
        return $this->belongsTo(Carrera::class);
    }

    public function tutor()
    {
        return $this->belongsTo(Tutor::class);
    }

    public function estudiantes()
    {
        return $this->hasMany(Estudiante::class);
    }
}
