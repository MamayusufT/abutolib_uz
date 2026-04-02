<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OlympiadQuestion extends Model
{
    protected $fillable = ['olympiad_id', 'question', 'type', 'difficulty', 'order'];

    public function olympiad()
    {
        return $this->belongsTo(Olympiads::class);
    }

    public function answers()
    {
        return $this->hasMany(OlympiadAnswer::class);
    }
}
