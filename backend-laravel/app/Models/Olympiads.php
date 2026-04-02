<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Olympiads extends Model
{
    protected $fillable = [
        'type',
        'name_uz',
        'lang',
        'description_uz',
        'questions_count',
        'starts_at',
        'ends_at',
        'duration',
        'pass_score',
        'max_attempts',
        'show_answers',
        'shuffle_questions',
        'shuffle_options',
        'is_active',
    ];

    protected $casts = [
        'starts_at'         => 'datetime',
        'ends_at'           => 'datetime',
        // show_answers — string: 'never' | 'after_submission' | 'after_finish'
        'shuffle_questions' => 'boolean',
        'shuffle_options'   => 'boolean',
        'is_active'         => 'boolean',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(OlympiadQuestion::class, 'olympiad_id');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(OlympiadRegistration::class, 'olympiad_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('starts_at', Carbon::today());
    }

    public function scopeLive($query)
    {
        $now = Carbon::now();
        return $query->where('starts_at', '<=', $now)->where('ends_at', '>=', $now);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('starts_at', '>', Carbon::now());
    }

    public function scopeFinished($query)
    {
        return $query->where('ends_at', '<', Carbon::now());
    }

    public function getStatusAttribute(): string
    {
        $now = Carbon::now();
        if ($this->starts_at > $now)                                return 'upcoming';
        if ($this->starts_at <= $now && $this->ends_at >= $now)    return 'live';
        return 'finished';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'upcoming' => 'Tez kunda',
            'live'     => 'Hozir davom etmoqda',
            'finished' => 'Tugagan',
            default    => '—',
        };
    }

    public function getTimeUntilStartAttribute(): ?string
    {
        if ($this->status !== 'upcoming') return null;
        $diff = Carbon::now()->diff($this->starts_at);
        if ($diff->days > 0) return "{$diff->days} kun qoldi";
        if ($diff->h > 0)    return "{$diff->h} soat qoldi";
        if ($diff->i > 0)    return "{$diff->i} daqiqa qoldi";
        return 'Tez boshlanadi';
    }

    public function getDurationLabelAttribute(): string
    {
        $minutes = (int) $this->duration;
        if ($minutes <= 0) return '—';
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;
        if ($h > 0 && $m > 0) return "{$h} soat {$m} daqiqa";
        if ($h > 0)            return "{$h} soat";
        return "{$m} daqiqa";
    }

    public function getRegistrationsCountAttribute(): int
    {
        return $this->registrations()->count();
    }
}
