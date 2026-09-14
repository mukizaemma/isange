<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountClosedDate extends Model
{
    protected $fillable = [
        'closed_on',
    ];

    protected $casts = [
        'closed_on' => 'date',
    ];
}
