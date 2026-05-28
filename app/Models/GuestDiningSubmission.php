<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestDiningSubmission extends Model
{
    protected $fillable = [
        'channel',
        'guest_name',
        'guest_phone',
        'guest_email',
        'special_requests',
        'currency',
        'items_json',
        'message_body',
        'grand_total_usd',
        'grand_total_rwf',
        'session_id',
    ];

    protected $casts = [
        'items_json' => 'array',
    ];
}
