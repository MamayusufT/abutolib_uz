<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OlympiadResult extends Model
{
    protected $fillable = [
        'olympiad_id', 'user_id', 'answers',
        'correct_answers', 'wrong_answers', 'skipped_questions',
        'total_questions', 'score_percent', 'time_spent',
        'attempt', 'status', 'finished_at',
    ];

    protected $casts = [
        'answers'     => 'array',
        'finished_at' => 'datetime',
    ];

    public function olympiad() { return $this->belongsTo(Olympiads::class); }
    public function user()     { return $this->belongsTo(User::class); }
}
