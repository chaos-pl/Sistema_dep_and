<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instrumento extends Model
{
    protected $table = 'instrumentos';

    protected $fillable = [
        'acronimo',
        'nombre',
    ];

    public function evaluaciones()
    {
        return $this->hasMany(Evaluacion::class, 'instrumento_id');
    }
}
