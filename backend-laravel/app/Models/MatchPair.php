<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchPair extends Model
{
    protected $fillable = ['question_id', 'left', 'right', 'order'];
}
