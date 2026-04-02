<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OlympiadRegistration extends Model
{
    protected $fillable = ['olympiad_id', 'user_id', 'registered_at'];

    protected $casts = ['registered_at' => 'datetime'];

    public function olympiad() { return $this->belongsTo(Olympiads::class); }
    public function user()     { return $this->belongsTo(User::class); }
}
