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
    public function index(Request $request): View
    {
        return $this->renderBookingsList($request);
    }

    public function search(Request $request): View
    {
        return $this->renderBookingsList($request);
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
        $filters = $this->filtersFromRequest($request);
        $bookings = $this->filteredBookingsQuery($filters)->get();

        return view('admin.pages.printBookings', [
            'bookings' => $bookings,
            'summary' => $this->buildSummary($bookings),
            'filters' => $filters,
            'start_date' => $filters['start_date'],
            'end_date' => $filters['end_date'],
        ]);
    }

    public function print()
    {
        return view('frontend.print.bookings');
    }

    private function renderBookingsList(Request $request): View
    {
        $filters = $this->filtersFromRequest($request);
        $bookings = $this->filteredBookingsQuery($filters)->get();

        return view('admin.bookings', [
            'bookings' => $bookings,
            'summary' => $this->buildSummary($bookings),
            'filters' => $filters,
            'start_date' => $filters['start_date'],
            'end_date' => $filters['end_date'],
        ]);
    }

    /**
     * @return array{start_date: ?string, end_date: ?string, channel: ?string, payment: ?string, status: ?string}
     */
    private function filtersFromRequest(Request $request): array
    {
        $channel = $request->input('channel');
        $payment = $request->input('payment');
        $status = $request->input('status');

        return [
            'start_date' => self::validDate($request->input('start_date')),
            'end_date' => self::validDate($request->input('end_date')),
            'channel' => in_array($channel, ['whatsapp', 'email'], true) ? $channel : null,
            'payment' => in_array($payment, ['pay_at_hotel', 'pay_directly', 'card'], true) ? $payment : null,
            'status' => in_array($status, [
                GuestBookingRequest::STATUS_PENDING,
                GuestBookingRequest::STATUS_CONFIRMED,
                GuestBookingRequest::STATUS_UNFORTUNATE,
                GuestBookingRequest::STATUS_REJECTED,
                GuestBookingRequest::STATUS_NO_SHOW,
            ], true) ? $status : null,
        ];
    }

    /**
     * @param  array{start_date: ?string, end_date: ?string, channel: ?string, payment: ?string, status: ?string}  $filters
     */
    private function filteredBookingsQuery(array $filters): Builder
    {
        $query = GuestBookingRequest::with('room')->latest();
        $this->applyDateFilter($query, $filters['start_date'], $filters['end_date']);

        if ($filters['channel']) {
            $query->where('fulfillment_choice', $filters['channel']);
        }

        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }

        if ($filters['payment'] === 'pay_at_hotel') {
            $query->where(function (Builder $inner) {
                $inner->where('payment_method', 'pay_at_hotel')
                    ->orWhereNull('payment_method')
                    ->orWhere('payment_method', '');
            });
        } elseif ($filters['payment'] === 'pay_directly') {
            $query->whereIn('payment_method', ['pay_directly', 'pay_direct', 'direct']);
        } elseif ($filters['payment'] === 'card') {
            $query->whereIn('payment_method', ['card', 'pay_by_card']);
        }

        return $query;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, GuestBookingRequest>  $bookings
     * @return array{total: int, pay_at_hotel: int, pay_directly: int, card: int, whatsapp: int, email: int, confirmed: int, pending: int, fully_booked: int, rejected: int, no_show: int, amount_usd: float}
     */
    private function buildSummary($bookings): array
    {
        $payAtHotel = $bookings->filter(function (GuestBookingRequest $booking) {
            $method = (string) ($booking->payment_method ?? '');

            return $method === '' || $method === 'pay_at_hotel';
        })->count();
        $payDirectly = $bookings->filter(function (GuestBookingRequest $booking) {
            return in_array((string) $booking->payment_method, ['pay_directly', 'pay_direct', 'direct'], true);
        })->count();
        $card = $bookings->filter(function (GuestBookingRequest $booking) {
            return in_array((string) $booking->payment_method, ['card', 'pay_by_card'], true);
        })->count();

        return [
            'total' => $bookings->count(),
            'pay_at_hotel' => $payAtHotel,
            'pay_directly' => $payDirectly,
            'card' => $card,
            'whatsapp' => $bookings->where('fulfillment_choice', 'whatsapp')->count(),
            'email' => $bookings->where('fulfillment_choice', 'email')->count(),
            'confirmed' => $bookings->where('status', GuestBookingRequest::STATUS_CONFIRMED)->count(),
            'pending' => $bookings->where('status', GuestBookingRequest::STATUS_PENDING)->count(),
            'fully_booked' => $bookings->where('status', GuestBookingRequest::STATUS_UNFORTUNATE)->count(),
            'rejected' => $bookings->where('status', GuestBookingRequest::STATUS_REJECTED)->count(),
            'no_show' => $bookings->where('status', GuestBookingRequest::STATUS_NO_SHOW)->count(),
            'amount_usd' => round((float) $bookings->sum(fn (GuestBookingRequest $booking) => (float) ($booking->total_usd ?? 0)), 2),
        ];
    }

    private function applyDateFilter(Builder $query, ?string $startDate, ?string $endDate): void
    {
        if ($startDate) {
            $query->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        }
        if ($endDate) {
            $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }
    }

    private static function validDate(mixed $value): ?string
    {
        $raw = is_string($value) ? trim($value) : '';
        if ($raw === '' || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $raw)) {
            return null;
        }

        return $raw;
    }
}
