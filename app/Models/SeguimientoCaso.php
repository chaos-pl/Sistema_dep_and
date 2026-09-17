<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeguimientoCaso extends Model
{
    protected $table = 'seguimientos_caso';

    protected $guarded = ['id'];

    public function autor()
    {
        return $this->belongsTo(User::class, 'autor_id');
    }

    public function responsable()
    {
        return $this->belongsTo(Psicologo::class, 'responsable_id');
    }
}
