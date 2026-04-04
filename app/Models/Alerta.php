<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alerta extends Model
{
    protected $table = 'alertas';

    protected $fillable = [
        'evaluacion_id',
        'estado',
    ];

    public function evaluacion()
    {
        return $this->belongsTo(Evaluacion::class);
    }
}
