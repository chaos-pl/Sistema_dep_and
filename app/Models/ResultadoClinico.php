<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultadoClinico extends Model
{
    protected $table = 'resultados_clinicos';

    protected $fillable = [
        'evaluacion_id',
        'puntaje_total',
        'nivel_riesgo',
    ];

    public function evaluacion()
    {
        return $this->belongsTo(Evaluacion::class, 'evaluacion_id');
    }
}
