<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $bookings = Booking::latest()->get();
        return view('admin.dashboard',['bookings'=>$bookings]);
    }
}
