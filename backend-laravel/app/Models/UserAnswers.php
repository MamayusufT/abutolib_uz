<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAnswers extends Model
{
    protected $fillable = ['user_id','question_id','answer_id','text_answer'];
}
