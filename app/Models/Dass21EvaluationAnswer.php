<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dass21EvaluationAnswer extends Model
{
    protected $table = 'dass21_evaluation_answers';

    protected $fillable = [
        'evaluation_id',
        'question_id',
        'score',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Relaciones                                                         */
    /* ------------------------------------------------------------------ */

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Dass21Evaluation::class, 'evaluation_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Dass21Question::class, 'question_id');
    }
}
