<?php

namespace App\Models;

use App\Support\FrontendPageCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Room extends Model
{
    use HasFactory;

    protected $table = 'rooms';

    public const TYPE_ROOM = 'room';

    public const TYPE_APARTMENT = 'apartment';

    protected $fillable = ['roomName', 'category', 'accommodation_type', 'image', 'slug', 'price', 'price_rwf', 'size', 'quantity', 'maxAdults', 'maxChildren', 'description'];

    protected static function booted(): void
    {
        static::saved(static function () {
            FrontendPageCache::forgetHomePage();
        });

        static::deleted(static function () {
            FrontendPageCache::forgetHomePage();
        });
    }

    protected $casts = [
        'price' => 'decimal:2',
        'price_rwf' => 'decimal:2',
    ];

    public function reservation()
    {
        return $this->hasMany(Reservation::class);
    }

    public function images()
    {
        return $this->hasMany(roomImage::class);
    }

    public function amenityOptions(): BelongsToMany
    {
        return $this->belongsToMany(HotelAmenityOption::class, 'hotel_amenity_room', 'room_id', 'hotel_amenity_option_id')->withTimestamps();
    }
}
