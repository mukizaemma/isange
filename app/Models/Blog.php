<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Blog extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'blogs';

    protected $fillable = [
        'title',
        'slug',
        'body',
        'image',
        'status',
        'published_by',
        'published_at',
        'views',
        'added_by',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'views' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(BlogComment::class)->latest();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'Published');
    }

    public function isPublished(): bool
    {
        return $this->status === 'Published';
    }

    public function imageUrl(): string
    {
        if (empty($this->image)) {
            return asset('assets/images/blog/blog1.jpg');
        }

        return asset('storage/images/blogs/'.ltrim($this->image, '/'));
    }

    public function excerpt(int $limit = 160): string
    {
        return Str::limit(trim(strip_tags((string) $this->body)), $limit);
    }

    public function readingTimeMinutes(): int
    {
        $words = str_word_count(strip_tags((string) $this->body));

        return max(1, (int) ceil($words / 200));
    }
}
