<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteAnalyticsEvent extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'event_key',
        'properties',
        'session_id',
    ];

    protected $casts = [
        'properties' => 'array',
    ];
}
