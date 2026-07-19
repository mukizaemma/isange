<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuestUpdateRecipient extends Model
{
    protected $fillable = [
        'guest_update_id',
        'user_id',
        'sent_at',
        'failure_reason',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function guestUpdate(): BelongsTo
    {
        return $this->belongsTo(GuestUpdate::class, 'guest_update_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
