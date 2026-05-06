<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Carrera extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre',
        'clave',
        'estado',
    ];

    public function grupos()
    {
        return $this->hasMany(Grupo::class);
    }
}
