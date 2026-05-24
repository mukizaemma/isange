<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;
    
    protected $table='blogs';

    protected $fillable = [
        'title',
        'slug',
        'body',
        'image',
        'status',
        'publish',
        'added_by',
        'published_at',
        'published_by',
        'created_by',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
