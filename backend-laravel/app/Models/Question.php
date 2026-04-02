<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'topic_id', 'question', 'difficulty', 'order','type'
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function matchPairs() { return $this->hasMany(MatchPair::class); }

    public function isMultiple(): bool
    {
        return $this->type === 'multiple';
    }

    public function isOpen(): bool
    {
        return $this->type === 'open';
    }

    public function checkAnswers(array $answerIds): bool
    {
        $correctIds = $this->answers()->where('is_correct', true)->pluck('id')->sort();
        return $correctIds->values()->toArray() === collect($answerIds)->sort()->values()->toArray();
    }
}
