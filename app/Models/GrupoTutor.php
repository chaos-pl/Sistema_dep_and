<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GrupoTutor extends Model
{
    use SoftDeletes;

    protected $table = 'grupo_tutor';

    protected $fillable = [
        'grupo_id',
        'tutor_id',
        'ciclo_escolar_id',
        'estado',
    ];

    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }

    public function tutor()
    {
        return $this->belongsTo(Tutor::class);
    }

    public function cicloEscolar()
    {
        return $this->belongsTo(CicloEscolar::class, 'ciclo_escolar_id');
    }
}
