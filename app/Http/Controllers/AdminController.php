<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $bookings = Booking::latest()->get();
        return view('admin.dashboard',['bookings'=>$bookings]);
    }

    public function users(){
        $users = User::all();

        return view('admin.users',[
            'users'=>$users
        ]);
    }

    public function makeAdmin($id){
        $user = User::find($id);
        $user->role = '1';
        $user->save();

        return redirect()->back()->with('success','User is now an admin');
    }
}
