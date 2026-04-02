<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestResult extends Model
{
    protected $fillable = [
        'user_id',
        'topic_id',
        'total_questions',
        'answered_questions',
        'correct_answers',
        'wrong_answers',
        'skipped_questions',
        'score_percent',
        'time_spent',
        'status',
        'answers',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'answers'     => 'array',
        'started_at'  => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class)->with('subject:id,name,slug,color');
    }
}
