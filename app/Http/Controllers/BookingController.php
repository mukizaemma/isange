<?php

namespace App\Http\Controllers;

use App\Models\Foodorder;
use App\Models\GuestBookingRequest;
use App\Models\Tablebooking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookings = GuestBookingRequest::with('room')->latest()->get();

        return view('admin.bookings', [
            'bookings' => $bookings,
        ]);
    }

    public function search(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        $query = GuestBookingRequest::with('room')->latest();

        if ($start_date && $end_date) {
            $start = Carbon::parse($start_date)->startOfDay();
            $end = Carbon::parse($end_date)->endOfDay();

            $query->whereBetween('created_at', [$start, $end]);
        }

        $bookings = $query->get();

        return view('admin.bookings', [
            'bookings' => $bookings,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ]);
    }

    public function TablesBookings()
    {
        $bookings = Tablebooking::latest()->get();

        return view('admin.bookingsTable', [
            'bookings' => $bookings,
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
        $booking = GuestBookingRequest::with('room')->findOrFail($id);

        return view('admin.bookingView', [
            'booking' => $booking,
        ]);
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
        $booking = GuestBookingRequest::findOrFail($id);
        $booking->delete();

        return redirect()->route('bookings')->with('success', 'Reservation deleted successfully.');
    }

    //  controller to Check the available rooms based on dates selected

    public function availableRooms(Request $request, $checkinDate)
    {
        $availRooms = DB::SELECT("SELECT * FROM rooms where id NOT IN(SELECT room_id FROM bookikings WHERE '$checkinDate' BETWEEN checkin AND checkout)");

        return response()->json(['data' => $availRooms]);
    }

    public function FoodOrders()
    {
        $orders = Foodorder::with('items')->get();

        return view('admin.foodOrders', [
            'orders' => $orders,
        ]);
    }

    public function export(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        $query = GuestBookingRequest::with('room')->latest();

        if ($start_date && $end_date) {
            $start = Carbon::parse($start_date)->startOfDay();
            $end = Carbon::parse($end_date)->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);
        }

        $bookings = $query->get();

        return view('admin.pages.printBookings', [
            'bookings' => $bookings,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ]);
    }

    public function print()
    {
        return view('frontend.print.bookings');
    }
}
