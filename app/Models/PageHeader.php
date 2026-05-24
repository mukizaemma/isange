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
        'intro_html',
        'body_html',
        'sections_json',
    ];

    protected $casts = [
        'sections_json' => 'array',
    ];
}
