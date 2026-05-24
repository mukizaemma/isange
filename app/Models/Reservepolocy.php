<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservepolocy extends Model
{
    use HasFactory;
    protected $table = 'reservepolocies';
    protected $fillable = ['title','description','details','cover','slug'];
}
