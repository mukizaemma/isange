<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GuestUpdate extends Model
{
    protected $fillable = [
        'created_by',
        'title',
        'cover_image',
        'description',
        'recipient_mode',
        'booking_from',
        'booking_to',
        'recipient_count',
        'sent_count',
        'sent_at',
    ];

    protected $casts = [
        'booking_from' => 'date',
        'booking_to' => 'date',
        'sent_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(GuestUpdateRecipient::class);
    }
}
