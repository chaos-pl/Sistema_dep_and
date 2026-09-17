<?php

namespace App\Models;

use App\Services\CasoAtencionService;
use Illuminate\Database\Eloquent\Model;

class Alerta extends Model
{
    protected static function booted(): void
    {
        static::created(fn (self $alerta) => app(CasoAtencionService::class)->forEvaluation($alerta->evaluacion));
    }

    protected $table = 'alertas';

    protected $fillable = [
        'evaluacion_id',
        'estado',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function evaluacion()
    {
        return $this->belongsTo(Evaluacion::class, 'evaluacion_id');
    }
}
