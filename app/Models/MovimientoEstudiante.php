<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoEstudiante extends Model
{
    protected $table = 'movimientos_estudiantes';

    protected $fillable = [
        'estudiante_id',
        'grupo_origen_id',
        'grupo_destino_id',
        'accion',
        'motivo',
        'observaciones',
        'realizado_por',
    ];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class);
    }

    public function grupoOrigen()
    {
        return $this->belongsTo(Grupo::class, 'grupo_origen_id');
    }

    public function grupoDestino()
    {
        return $this->belongsTo(Grupo::class, 'grupo_destino_id');
    }

    public function realizadoPor()
    {
        return $this->belongsTo(User::class, 'realizado_por');
    }
}
