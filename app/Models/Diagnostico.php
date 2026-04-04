<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diagnostico extends Model
{
    protected $table = 'diagnosticos';

    protected $fillable = [
        'evaluacion_id',
        'psicologo_id',
        'impresion_diagnostica',
        'retroalimentacion_estudiante',
        'requiere_derivacion',
    ];

    protected function casts(): array
    {
        return [
            'requiere_derivacion' => 'boolean',
        ];
    }

    public function evaluacion()
    {
        return $this->belongsTo(Evaluacion::class);
    }

    public function psicologo()
    {
        return $this->belongsTo(Psicologo::class);
    }
}
