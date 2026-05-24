<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class HotelAmenityOption extends Model
{
    protected $fillable = ['label', 'sort_order'];

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class, 'hotel_amenity_room', 'hotel_amenity_option_id', 'room_id')->withTimestamps();
    }
}
