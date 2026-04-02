<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OlympiadAnswer extends Model
{
    protected $fillable = ['olympiad_question_id', 'answer', 'is_correct'];

    protected $casts = ['is_correct' => 'boolean'];

    public function question(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(OlympiadQuestion::class);
    }
}
