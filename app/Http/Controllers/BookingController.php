<?php

namespace App\Http\Controllers;

use App\Models\Foodorder;
use App\Models\GuestBookingRequest;
use App\Models\SiteAnalyticsEvent;
use App\Models\Tablebooking;
use App\Support\BookingEmailSender;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return $this->renderBookingsList();
    }

    public function search(Request $request): View
    {
        return $this->renderBookingsList(
            $request->input('start_date'),
            $request->input('end_date')
        );
    }

    public function TablesBookings()
    {
        $bookings = Tablebooking::latest()->get();

        return view('admin.bookingsTable', [
            'bookings' => $bookings,
        ]);
    }

    public function create()
    {
        return view('admin.testBooking');
    }

    public function store(Request $request)
    {
    }

    public function viewBooking($id): View
    {
        $booking = GuestBookingRequest::with('room')->findOrFail($id);

        return view('admin.bookingView', [
            'booking' => $booking,
        ]);
    }

    public function updateStatus(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(GuestBookingRequest::REVIEWABLE_STATUSES)],
            'admin_message' => ['nullable', 'string', 'max:5000'],
            'notify_guest' => ['sometimes', 'boolean'],
        ]);

        $booking = GuestBookingRequest::with('room')->findOrFail($id);
        $newStatus = $validated['status'];

        if ($booking->isPending()) {
            // All review outcomes allowed from pending.
        } elseif ($booking->isConfirmed() && $newStatus === GuestBookingRequest::STATUS_NO_SHOW) {
            // Confirmed bookings can later be marked no-show.
        } else {
            return back()->with('error', 'This booking cannot be changed to that status.');
        }

        $now = now();
        $booking->update([
            'status' => $newStatus,
            'admin_message' => $validated['admin_message'] ?? null,
            'reviewed_at' => $now,
            'confirmed_at' => $newStatus === GuestBookingRequest::STATUS_CONFIRMED ? $now : $booking->confirmed_at,
        ]);
        $booking->refresh();

        $notifyGuest = $request->boolean('notify_guest', true);
        $emailSent = false;
        $hasGuestEmail = filter_var(trim((string) $booking->guest_email), FILTER_VALIDATE_EMAIL);

        if ($notifyGuest && $hasGuestEmail) {
            $emailSent = BookingEmailSender::sendGuestStatusUpdate($booking, $newStatus);
        }

        SiteAnalyticsEvent::create([
            'event_key' => 'booking_admin_status_updated',
            'properties' => [
                'status' => $newStatus,
                'fulfillment' => $booking->fulfillment_choice,
                'guest_email' => $emailSent,
                'source' => 'reservations',
            ],
            'session_id' => null,
        ]);

        $label = GuestBookingRequest::statusLabel($newStatus);

        if ($notifyGuest && $hasGuestEmail && ! $emailSent) {
            return back()->with('warning', "Booking marked as {$label}, but the notification email could not be sent to the guest.");
        }

        if ($notifyGuest && $hasGuestEmail && $emailSent) {
            return back()->with('success', "Booking marked as {$label} and email sent to {$booking->guest_email}.");
        }

        if ($notifyGuest && ! $hasGuestEmail) {
            return back()->with('success', "Booking marked as {$label}. No guest email on file, so no notification was sent.");
        }

        return back()->with('success', "Booking marked as {$label}.");
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id): RedirectResponse
    {
        $booking = GuestBookingRequest::findOrFail($id);
        $booking->delete();

        return redirect()->route('bookings')->with('success', 'Reservation deleted successfully.');
    }

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
        $this->applyDateFilter($query, $start_date, $end_date);
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

    private function renderBookingsList(?string $startDate = null, ?string $endDate = null): View
    {
        $query = GuestBookingRequest::with('room')->latest();
        $this->applyDateFilter($query, $startDate, $endDate);
        $bookings = $query->get();

        return view('admin.bookings', [
            'bookings' => $bookings,
            'summary' => $this->buildSummary($bookings),
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, GuestBookingRequest>  $bookings
     * @return array{total: int, whatsapp: int, email: int, confirmed: int, pending: int, fully_booked: int, rejected: int, no_show: int}
     */
    private function buildSummary($bookings): array
    {
        return [
            'total' => $bookings->count(),
            'whatsapp' => $bookings->where('fulfillment_choice', 'whatsapp')->count(),
            'email' => $bookings->where('fulfillment_choice', 'email')->count(),
            'confirmed' => $bookings->where('status', GuestBookingRequest::STATUS_CONFIRMED)->count(),
            'pending' => $bookings->where('status', GuestBookingRequest::STATUS_PENDING)->count(),
            'fully_booked' => $bookings->where('status', GuestBookingRequest::STATUS_UNFORTUNATE)->count(),
            'rejected' => $bookings->where('status', GuestBookingRequest::STATUS_REJECTED)->count(),
            'no_show' => $bookings->where('status', GuestBookingRequest::STATUS_NO_SHOW)->count(),
        ];
    }

    private function applyDateFilter(Builder $query, ?string $startDate, ?string $endDate): void
    {
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);
        }
    }
}
