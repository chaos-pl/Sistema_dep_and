<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CicloEscolar extends Model
{
    use SoftDeletes;

    protected $table = 'ciclos_escolares';

    protected $fillable = [
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
        ];
    }

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'ciclo_escolar_id');
    }
}
