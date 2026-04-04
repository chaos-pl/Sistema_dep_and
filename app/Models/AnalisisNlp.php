<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalisisNlp extends Model
{
    protected $table = 'analisis_nlp';

    protected $fillable = [
        'codigo_anonimo',
        'texto_ingresado',
        'etiqueta_roberta',
        'score_confianza',
        'requiere_atencion',
    ];
}
