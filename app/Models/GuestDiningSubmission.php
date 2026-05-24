<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestDiningSubmission extends Model
{
    protected $fillable = [
        'channel',
        'items_json',
        'message_body',
        'grand_total_usd',
        'session_id',
    ];

    protected $casts = [
        'items_json' => 'array',
    ];
}
