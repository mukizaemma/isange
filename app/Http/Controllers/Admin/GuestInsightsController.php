<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuestBookingRequest;
use App\Models\GuestDiningSubmission;
use App\Models\SiteAnalyticsEvent;
use App\Support\BookingEmailSender;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GuestInsightsController extends Controller
{
    public function index(): View
    {
        $eventTotals = SiteAnalyticsEvent::query()
            ->selectRaw('event_key, COUNT(*) as total')
            ->groupBy('event_key')
            ->orderByDesc('total')
            ->get();

        $bookingRequests = GuestBookingRequest::query()->with('room')->latest()->limit(100)->get();
        $diningSubmissions = GuestDiningSubmission::query()->latest()->limit(100)->get();

        $paymentMethodTotals = GuestBookingRequest::query()
            ->selectRaw('COALESCE(payment_method, fulfillment_choice) as method, COUNT(*) as total')
            ->groupBy('method')
            ->orderByDesc('total')
            ->get();

        $monthlyPaymentReport = GuestBookingRequest::query()
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, payment_method, COUNT(*) as total')
            ->whereNotNull('payment_method')
            ->groupBy('month', 'payment_method')
            ->orderByDesc('month')
            ->limit(24)
            ->get();

        return view('admin.guest-insights', compact(
            'eventTotals',
            'bookingRequests',
            'diningSubmissions',
            'paymentMethodTotals',
            'monthlyPaymentReport',
        ));
    }

    public function updateBookingStatus(Request $request, string $publicId): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(GuestBookingRequest::REVIEWABLE_STATUSES)],
        ]);

        $booking = GuestBookingRequest::query()->with('room')->where('public_id', $publicId)->firstOrFail();
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
            'reviewed_at' => $now,
            'confirmed_at' => $newStatus === GuestBookingRequest::STATUS_CONFIRMED ? $now : $booking->confirmed_at,
        ]);

        $emailSent = false;
        if ($booking->fulfillment_choice === 'email') {
            $emailSent = BookingEmailSender::sendGuestStatusUpdate($booking, $newStatus);
        }

        SiteAnalyticsEvent::create([
            'event_key' => 'booking_admin_status_updated',
            'properties' => [
                'status' => $newStatus,
                'fulfillment' => $booking->fulfillment_choice,
                'guest_email' => $emailSent,
            ],
            'session_id' => null,
        ]);

        $label = GuestBookingRequest::statusLabel($newStatus);

        if ($booking->fulfillment_choice === 'email' && ! $emailSent) {
            return back()->with('warning', "Booking marked as {$label}, but the notification email could not be sent to the guest.");
        }

        $message = $booking->fulfillment_choice === 'email'
            ? "Booking marked as {$label} and email sent to {$booking->guest_email}."
            : "Booking marked as {$label}. No email sent (WhatsApp booking).";

        return back()->with('success', $message);
    }
}
