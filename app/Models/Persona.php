<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $fillable = [
        'user_id',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'fecha_nacimiento',
        'genero',
        'telefono',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function estudiante()
    {
        return $this->hasOne(Estudiante::class);
    }
    public function tutor()
    {
        return $this->hasOne(Tutor::class);
    }

    public function psicologo()
    {
        return $this->hasOne(Psicologo::class);
    }
}
