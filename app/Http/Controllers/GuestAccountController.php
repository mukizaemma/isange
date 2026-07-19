<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuestAccountController extends Controller
{
    public function bookings(Request $request): View
    {
        abort_unless($request->user()->isGuest(), 403);

        $bookings = $request->user()
            ->bookings()
            ->with('room')
            ->latest()
            ->paginate(15);

        return view('frontend.guest-bookings', compact('bookings'));
    }

    public function updates(Request $request): View
    {
        abort_unless($request->user()->isGuest(), 403);

        $updates = $request->user()
            ->updateRecipients()
            ->whereNotNull('sent_at')
            ->with('guestUpdate')
            ->latest('sent_at')
            ->paginate(12);

        return view('frontend.guest-updates', compact('updates'));
    }

    public function unsubscribe(string $token): RedirectResponse
    {
        $user = User::where('marketing_unsubscribe_token', $token)->firstOrFail();
        $user->forceFill([
            'marketing_opt_in' => false,
            'marketing_consented_at' => null,
        ])->save();

        return redirect()->route('home')->with('success', 'You have been unsubscribed from guest updates.');
    }
}
