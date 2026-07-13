<?php

namespace App\Http\Controllers;

use App\Models\GuestBookingRequest;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $bookings = GuestBookingRequest::with('room')->latest()->get();

        return view('admin.dashboard', ['bookings' => $bookings]);
    }
}
