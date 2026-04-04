<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Psicologo extends Model
{
    protected $table = 'psicologos';

    protected $fillable = [
        'persona_id',
        'cedula_profesional',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function diagnosticos()
    {
        return $this->hasMany(Diagnostico::class);
    }
}
