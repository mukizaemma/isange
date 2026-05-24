<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiningGalleryImage extends Model
{
    protected $fillable = ['image', 'caption', 'sort_order'];
}
