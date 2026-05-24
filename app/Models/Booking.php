<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $table = 'bookings';
    protected $fillable = ['names','phone','email','room_id','checkin','checkout','adults','rooms','children','nights','total','address','status','description'];

    public function room(){
        return $this->belongsTo(Room::class);
    }
}
