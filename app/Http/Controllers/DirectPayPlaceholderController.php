<?php

namespace App\Http\Controllers;

use App\Models\GuestBookingRequest;
use App\Models\Room;
use App\Models\SiteAnalyticsEvent;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class DirectPayPlaceholderController extends Controller
{
    use Concerns\RendersSpaFragment;

    public function __invoke(Request $request): View|Response
    {
        SiteAnalyticsEvent::create([
            'event_key' => 'direct_pay_page_view',
            'properties' => [],
            'session_id' => substr(sha1($request->session()->getId()), 0, 40),
        ]);

        $booking = null;
        $bookingId = $request->query('booking') ?? session('booking_public_id');
        if ($bookingId) {
            $booking = GuestBookingRequest::with('room')->where('public_id', $bookingId)->first();
        }

        $room = $booking?->room;
        if (! $room && $request->filled('room')) {
            $room = Room::where('slug', $request->query('room'))->first();
        }

        return $this->spaView('frontend.pay-dpo-placeholder', compact('room', 'booking'), 'Book and pay');
    }
}
