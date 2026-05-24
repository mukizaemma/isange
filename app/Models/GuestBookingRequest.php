<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class GuestBookingRequest extends Model
{
    protected $fillable = [
        'public_id',
        'room_id',
        'check_in',
        'check_out',
        'airport_pickup',
        'airport_dropoff',
        'additional_requests',
        'guest_name',
        'guest_phone',
        'guest_email',
        'guest_country',
        'fulfillment_choice',
        'completed_channel',
        'message_body',
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'airport_pickup' => 'boolean',
        'airport_dropoff' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (GuestBookingRequest $model): void {
            if (empty($model->public_id)) {
                $model->public_id = (string) Str::uuid();
            }
        });
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
