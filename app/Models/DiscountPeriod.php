<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountPeriod extends Model
{
    protected $fillable = [
        'starts_on',
        'ends_on',
        'discount_type',
        'discount_value',
    ];

    protected $casts = [
        'starts_on' => 'date',
        'ends_on' => 'date',
        'discount_value' => 'decimal:2',
    ];
};
