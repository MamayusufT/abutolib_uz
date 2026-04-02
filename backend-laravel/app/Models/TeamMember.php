<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    protected $fillable = [
        'name', 'role', 'avatar', 'email',
        'telegram', 'instagram', 'bio', 'order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
