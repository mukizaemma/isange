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
}
