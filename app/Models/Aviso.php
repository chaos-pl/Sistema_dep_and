<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aviso extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['leido_at' => 'datetime'];
    }

    public function casoAtencion()
    {
        return $this->belongsTo(CasoAtencion::class);
    }

    public function evaluacion()
    {
        return $this->belongsTo(Evaluacion::class);
    }

    public function getTituloAttribute(): string
    {
        return $this->tipo === 'retroalimentacion' ? 'Tienes nueva retroalimentación' : 'Hay un nuevo caso de atención';
    }
}
