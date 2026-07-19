<?php

namespace App\Models;

use App\Support\FrontendPageCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    use HasFactory;

    protected $table = 'slides';

    protected $fillable = ['image', 'category', 'heading', 'subheading', 'sort_order'];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function imageUrl(): ?string
    {
        if (empty($this->image)) {
            return null;
        }

        return asset('storage/images/slides/'.ltrim($this->image, '/'));
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('created_at')->orderBy('id');
    }

    protected static function booted(): void
    {
        static::creating(static function (Slide $slide): void {
            if ($slide->sort_order === null) {
                $slide->sort_order = ((int) static::query()->max('sort_order')) + 1;
            }
        });

        static::saved(static function () {
            FrontendPageCache::forgetHomePage();
        });

        static::deleted(static function () {
            FrontendPageCache::forgetHomePage();
        });
    }
}
