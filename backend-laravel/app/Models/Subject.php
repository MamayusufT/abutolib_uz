<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Subject extends Model
{
    protected $fillable = [
        'name', 'slug', 'image', 'description', 'color', 'order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($subject) {
            if (empty($subject->slug)) {
                $subject->slug = Str::slug($subject->name);
            }
        });
    }

    public function topics(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Topic::class);
    }

    public function getImageAttribute($value)
    {
        if (!$value) return null;

        return asset('storage/' . $value);
    }
    
    public function getQuestionsCountAttribute(): int
    {
        return Question::whereIn('topic_id', $this->topics()->pluck('id'))->count();
    }

    public function files(): HasMany
    {
        return $this->hasMany(SubjectFile::class);
    }
}
