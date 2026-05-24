<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuestBookingRequest;
use App\Models\GuestDiningSubmission;
use App\Models\SiteAnalyticsEvent;
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

        return view('admin.guest-insights', compact('eventTotals', 'bookingRequests', 'diningSubmissions'));
    }
}
