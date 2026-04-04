<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tutor extends Model
{
    protected $table = 'tutores';

    protected $fillable = [
        'persona_id',
        'numero_empleado',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function grupos()
    {
        return $this->hasMany(Grupo::class);
    }
}
