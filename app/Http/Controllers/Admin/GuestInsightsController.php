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
            'admin_message' => ['nullable', 'string', 'max:5000'],
            'notify_guest' => ['sometimes', 'boolean'],
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
            'admin_message' => array_key_exists('admin_message', $validated)
                ? ($validated['admin_message'] ?: null)
                : $booking->admin_message,
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
                'source' => 'guest_insights',
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
}
