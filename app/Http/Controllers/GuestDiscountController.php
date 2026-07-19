<?php

namespace App\Http\Controllers;

use App\Models\GuestBookingRequest;
use App\Models\User;
use App\Support\GuestEmailSender;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class GuestDiscountController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $request->session()->put('guest_discount_return', route('booking.checkout'));

        if ($request->user()?->hasUnlockedDiscount()) {
            return redirect()->route('booking.checkout')->with('success', 'Your direct-booking discount is unlocked.');
        }
        if ($request->user()?->isGuest()) {
            $user = $request->user();
            if (! $user->email_otp_hash || ! $user->email_otp_expires_at || $user->email_otp_expires_at->isPast()) {
                $this->issueOtp($user);
            }

            return redirect()->route('guest.discount.verify');
        }

        return view('frontend.guest-discount');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'marketing_opt_in' => ['nullable', 'boolean'],
        ]);

        $email = strtolower(trim($validated['email']));
        $existing = User::where('email', $email)->first();
        if ($existing) {
            if (! $existing->isGuest() || ! Hash::check($validated['password'], $existing->password)) {
                return back()->withErrors(['email' => 'An account already uses this email. Sign in with its password instead.'])->withInput();
            }

            $user = $existing;
            if ($user->email_verified_at) {
                Auth::login($user, true);

                return redirect()->route('booking.checkout')->with('success', 'Your direct-booking discount is unlocked.');
            }
        } else {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $email,
                'password' => Hash::make($validated['password']),
                'role' => User::ROLE_GUEST,
                'marketing_opt_in' => $request->boolean('marketing_opt_in'),
                'marketing_consented_at' => $request->boolean('marketing_opt_in') ? now() : null,
                'marketing_unsubscribe_token' => Str::random(64),
            ]);
        }

        Auth::login($user, true);
        $sent = $this->issueOtp($user);

        return redirect()->route('guest.discount.verify')
            ->with($sent ? 'success' : 'error', $sent
                ? 'We sent a 4-digit code to '.$user->email.'.'
                : 'Your account was created, but the verification email could not be sent. You may continue booking at the regular price.');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt(['email' => strtolower(trim($credentials['email'])), 'password' => $credentials['password']], true)) {
            return back()->withErrors(['email' => 'The email or password is incorrect.'])->withInput();
        }

        $request->session()->regenerate();
        $user = $request->user();
        if (! $user->isGuest()) {
            Auth::logout();

            return back()->withErrors(['email' => 'Use the staff login page for this account.']);
        }

        if ($user->hasUnlockedDiscount()) {
            return redirect()->route('booking.checkout')->with('success', 'Your direct-booking discount is unlocked.');
        }

        $sent = $this->issueOtp($user);

        return redirect()->route('guest.discount.verify')
            ->with($sent ? 'success' : 'error', $sent
                ? 'We sent a new 4-digit code to '.$user->email.'.'
                : 'We could not send a verification code. You may continue at the regular price.');
    }

    public function verifyForm(Request $request): View|RedirectResponse
    {
        if (! $request->user()?->isGuest()) {
            return redirect()->route('guest.discount');
        }
        if ($request->user()->hasUnlockedDiscount()) {
            return redirect()->route('booking.checkout');
        }

        return view('frontend.guest-discount-verify');
    }

    public function verify(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'digits:4'],
        ]);
        $user = $request->user();
        if (! $user?->isGuest()) {
            return redirect()->route('guest.discount');
        }

        if (! $user->email_otp_hash || ! $user->email_otp_expires_at || $user->email_otp_expires_at->isPast()) {
            return back()->withErrors(['code' => 'This code has expired. Request a new one.']);
        }
        if ($user->email_otp_attempts >= 5) {
            return back()->withErrors(['code' => 'Too many incorrect attempts. Request a new code.']);
        }
        if (! Hash::check($validated['code'], $user->email_otp_hash)) {
            $user->increment('email_otp_attempts');

            return back()->withErrors(['code' => 'The code is incorrect.']);
        }

        $user->forceFill([
            'email_verified_at' => now(),
            'email_otp_hash' => null,
            'email_otp_expires_at' => null,
            'email_otp_attempts' => 0,
        ])->save();

        GuestBookingRequest::query()
            ->whereNull('user_id')
            ->whereRaw('LOWER(guest_email) = ?', [strtolower($user->email)])
            ->update(['user_id' => $user->id]);

        return redirect()->route('booking.checkout')
            ->with('success', 'Email confirmed. Your cart was kept and discounted room prices are now active.');
    }

    public function resend(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user?->isGuest() || $user->hasUnlockedDiscount()) {
            return redirect()->route('booking.checkout');
        }

        return back()->with(
            $this->issueOtp($user) ? 'success' : 'error',
            'A new verification code has been requested.'
        );
    }

    private function issueOtp(User $user): bool
    {
        $code = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        $user->forceFill([
            'email_otp_hash' => Hash::make($code),
            'email_otp_expires_at' => now()->addMinutes(10),
            'email_otp_attempts' => 0,
        ])->save();

        return GuestEmailSender::sendOtp($user, $code);
    }
}
