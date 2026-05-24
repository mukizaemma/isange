<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roomcart extends Model
{
    use HasFactory;

    protected $table = 'roomcarts';
    protected $fillable = ['product','quantity','names','email','phone','address','checkin','checkout','adults','children','description','status'];
}
