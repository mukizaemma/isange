<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuestBookingRequest;
use App\Models\GuestUpdate;
use App\Models\User;
use App\Support\GuestEmailSender;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GuestController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);
        $from = $validated['from'] ?? null;
        $to = $validated['to'] ?? null;

        $bookingScope = static function (Builder $query) use ($from, $to): void {
            $query
                ->when($from, fn (Builder $q) => $q->whereDate('created_at', '>=', $from))
                ->when($to, fn (Builder $q) => $q->whereDate('created_at', '<=', $to));
        };

        $guests = User::query()
            ->where('role', User::ROLE_GUEST)
            ->withCount(['bookings as booking_count' => $bookingScope])
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $returningGuests = User::query()
            ->where('role', User::ROLE_GUEST)
            ->whereHas('bookings', $bookingScope, '>=', 2)
            ->count();

        $bookingOnlyQuery = GuestBookingRequest::query()
            ->whereNull('user_id')
            ->whereNotNull('guest_email')
            ->where('guest_email', '<>', '')
            ->when($from, fn (Builder $q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn (Builder $q) => $q->whereDate('created_at', '<=', $to));

        $bookingOnlyGuests = (clone $bookingOnlyQuery)
            ->selectRaw('LOWER(guest_email) as email, MAX(guest_name) as name, COUNT(*) as booking_count, MAX(created_at) as latest_booking')
            ->groupByRaw('LOWER(guest_email)')
            ->orderByDesc('latest_booking')
            ->limit(100)
            ->get();

        $returningGuests += (clone $bookingOnlyQuery)
            ->selectRaw('LOWER(guest_email)')
            ->groupByRaw('LOWER(guest_email)')
            ->havingRaw('COUNT(*) >= 2')
            ->get()
            ->count();

        $updates = GuestUpdate::latest()->limit(20)->get();

        return view('admin.guests.index', compact(
            'guests',
            'bookingOnlyGuests',
            'returningGuests',
            'updates',
            'from',
            'to',
        ));
    }

    public function sendUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'cover_image' => ['nullable', 'image', 'max:5120'],
            'description' => ['required', 'string', 'max:20000'],
            'recipient_mode' => ['required', Rule::in(['selected', 'date_range'])],
            'guest_ids' => ['required_if:recipient_mode,selected', 'array'],
            'guest_ids.*' => ['integer', 'exists:users,id'],
            'booking_from' => ['required_if:recipient_mode,date_range', 'nullable', 'date'],
            'booking_to' => ['required_if:recipient_mode,date_range', 'nullable', 'date', 'after_or_equal:booking_from'],
        ]);

        $query = User::query()
            ->where('role', User::ROLE_GUEST)
            ->whereNotNull('email_verified_at')
            ->where('marketing_opt_in', true);

        if ($validated['recipient_mode'] === 'selected') {
            $query->whereIn('id', $validated['guest_ids'] ?? []);
        } else {
            $query->whereHas('bookings', function (Builder $booking) use ($validated): void {
                $booking
                    ->whereDate('created_at', '>=', $validated['booking_from'])
                    ->whereDate('created_at', '<=', $validated['booking_to']);
            });
        }

        $recipients = $query->get();
        if ($recipients->isEmpty()) {
            return back()->withInput()->with('warning', 'No verified, opted-in guests matched this selection.');
        }

        $coverPath = $request->file('cover_image')?->store('guest-updates', 'public');
        $update = GuestUpdate::create([
            'created_by' => $request->user()->id,
            'title' => $validated['title'],
            'cover_image' => $coverPath,
            'description' => $validated['description'],
            'recipient_mode' => $validated['recipient_mode'],
            'booking_from' => $validated['booking_from'] ?? null,
            'booking_to' => $validated['booking_to'] ?? null,
            'recipient_count' => $recipients->count(),
        ]);

        $sent = 0;
        foreach ($recipients as $guest) {
            $recipient = $update->recipients()->create(['user_id' => $guest->id]);
            if (GuestEmailSender::sendUpdate($guest, $update)) {
                $recipient->update(['sent_at' => now()]);
                $sent++;
            } else {
                $recipient->update(['failure_reason' => 'Email provider rejected or could not deliver the request.']);
            }
        }

        $update->update([
            'sent_count' => $sent,
            'sent_at' => now(),
        ]);

        return back()->with('success', "Update sent to {$sent} of {$recipients->count()} eligible guests.");
    }
}
