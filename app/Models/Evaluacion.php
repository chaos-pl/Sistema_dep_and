<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluacion extends Model
{
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

    public function respuestas()
    {
        return $this->hasMany(Respuesta::class, 'evaluacion_id');
    }

    public function resultadoClinico()
    {
        return $this->hasOne(ResultadoClinico::class, 'evaluacion_id');
    }
}
