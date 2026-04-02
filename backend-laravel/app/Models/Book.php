<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'author',
        'isbn',
        'price',
        'pages',
        'language',
        'status',
        'cover_image',
        'pdf_file',
        'download_count',
        'view_count',
        'rating',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'rating' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $appends = ['average_rating', 'review_count'];

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 2);
    }

    public function getReviewCountAttribute(): int
    {
        return $this->reviews()->count();
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    public function incrementDownloadCount(): void
    {
        $this->increment('download_count');
    }
}
