<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Foodorder;
use App\Models\Roombooking;
use App\Models\Tablebooking;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookings = Booking::with('room')->latest()->get();
        return view('admin.bookings',[
            'bookings'=>$bookings
        ]);
    }
    public function search(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
    
        $bookings = Roombooking::whereBetween('created_at', [$start_date, $end_date])->get();

        return view('admin.pages.printBookings',[
            'bookings'=>$bookings,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ]);
    }

    public function TablesBookings()
    {
        $bookings = Tablebooking::latest()->get();
        return view('admin.bookingsTable',[
            'bookings'=>$bookings
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.testBooking');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

  
    }

    /**
     * Display the specified resource.
     */
    public function viewBooking($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

//  controller to Check the available rooms based on dates selected

public function availableRooms(Request $request, $checkinDate){
    $availRooms = DB::SELECT("SELECT * FROM rooms where id NOT IN(SELECT room_id FROM bookikings WHERE '$checkinDate' BETWEEN checkin AND checkout)");
    return response()->json(['data'=>$availRooms]);
}


public function FoodOrders(){
    $orders = Foodorder::with('items')->get();
    return view('admin.foodOrders',[
        'orders'=>$orders
    ]);
}
public function export(Request $request)
{
    $start_date = $request->input('start_date');
    $end_date = $request->input('end_date');

    $bookings = Roombooking::whereBetween('created_at', [$start_date, $end_date])->get();

    // return Excel::download(new BookingsExport($bookings), 'bookings.xlsx');
}

public function print(){
    return view('frontend.print.bookings');
}

}
