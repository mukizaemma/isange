<?php

namespace App\Models;

use App\Support\FrontendPageCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogComment extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saved(fn () => FrontendPageCache::forgetHomePage());
        static::deleted(fn () => FrontendPageCache::forgetHomePage());
    }

    protected $fillable = [
        'blog_id',
        'author_name',
        'author_email',
        'body',
    ];

    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class);
    }
}
