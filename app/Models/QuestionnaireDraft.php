<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionnaireDraft extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['answers' => 'array', 'version' => 'integer', 'completed' => 'boolean'];
    }
}
