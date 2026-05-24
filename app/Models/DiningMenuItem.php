<?php

namespace App\Models;

use App\Support\FrontendPageCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiningMenuItem extends Model
{
    protected $fillable = ['title', 'description', 'price_usd', 'price_rwf', 'prep_minutes', 'image', 'sort_order', 'menu_category_id'];

    protected static function booted(): void
    {
        static::saved(static function () {
            FrontendPageCache::forgetDiningAndHome();
        });

        static::deleted(static function () {
            FrontendPageCache::forgetDiningAndHome();
        });
    }

    protected $casts = [
        'price_usd' => 'decimal:2',
        'price_rwf' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(MenuCategory::class, 'menu_category_id');
    }
}
