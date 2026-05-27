<?php

namespace App\Models;

use App\Support\FrontendPageCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    use HasFactory;

    protected $table = 'slides';

    protected $fillable = ['image', 'category', 'heading', 'subheading'];

    public function imageUrl(): ?string
    {
        if (empty($this->image)) {
            return null;
        }

        return asset('storage/images/slides/'.ltrim($this->image, '/'));
    }

    protected static function booted(): void
    {
        static::saved(static function () {
            FrontendPageCache::forgetHomePage();
        });

        static::deleted(static function () {
            FrontendPageCache::forgetHomePage();
        });
    }
}
