<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dass21Question extends Model
{
    protected $table = 'dass21_questions';

    protected $fillable = [
        'instrument_id',
        'item_number',
        'statement',
        'dimension',
    ];

    protected function casts(): array
    {
        return [
            'item_number' => 'integer',
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Relaciones                                                         */
    /* ------------------------------------------------------------------ */

    public function instrumento(): BelongsTo
    {
        return $this->belongsTo(Instrumento::class, 'instrument_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Dass21EvaluationAnswer::class, 'question_id');
    }

    /* ------------------------------------------------------------------ */
    /*  Scopes                                                             */
    /* ------------------------------------------------------------------ */

    public function scopeByDimension($query, string $dimension)
    {
        return $query->where('dimension', $dimension);
    }
}
