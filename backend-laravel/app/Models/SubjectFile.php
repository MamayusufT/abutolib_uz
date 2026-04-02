<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectFile extends Model
{
    protected $fillable = [
        'subject_id', 'file_path', 'file_name',
        'file_type', 'file_size'
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function getFilePathAttribute($value)
    {
        return asset('storage/' . $value);
    }

    public function getFullUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }
}
