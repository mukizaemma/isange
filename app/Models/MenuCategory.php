<?php

namespace App\Models;

use App\Support\FrontendPageCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuCategory extends Model
{
    protected $fillable = ['name', 'sort_order', 'cover_image'];

    protected static function booted(): void
    {
        static::saved(static function () {
            FrontendPageCache::forgetDiningAndHome();
        });

        static::deleted(static function () {
            FrontendPageCache::forgetDiningAndHome();
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(DiningMenuItem::class, 'menu_category_id')->orderBy('sort_order')->orderBy('title');
    }
}
