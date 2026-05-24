<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageHeader extends Model
{
    protected $fillable = [
        'page_key',
        'label',
        'title',
        'subtitle',
        'hero_image',
    ];
}
