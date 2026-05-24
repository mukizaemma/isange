<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $table = 'services';
    protected $fillable = ['title','description','image','slug'];

    function serviceImages(){
        return $this->hasMany(ServiceImage::class);
    }
}
