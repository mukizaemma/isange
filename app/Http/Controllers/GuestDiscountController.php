<?php

namespace App\Http\Controllers;

use App\Models\GuestBookingRequest;
use App\Models\User;
use App\Support\GuestEmailSender;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class GuestDiscountController extends Controller
{
    private const MAX_OTP_ATTEMPTS = 3;

    public function requestCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'marketing_opt_in' => ['nullable', 'boolean'],
        ]);

        $email = strtolower(trim($validated['email']));
        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if ($user && ! $user->isGuest()) {
            return response()->json([
                'message' => 'This email cannot be used for guest discount access.',
            ], 422);
        }

        if (! $user) {
            $name = Str::headline(Str::before($email, '@')) ?: 'Guest';
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(Str::random(40)),
                'role' => User::ROLE_GUEST,
                'marketing_opt_in' => $request->boolean('marketing_opt_in'),
                'marketing_consented_at' => $request->boolean('marketing_opt_in') ? now() : null,
                'marketing_unsubscribe_token' => Str::random(64),
            ]);
        } elseif ($request->boolean('marketing_opt_in')) {
            $user->forceFill([
                'marketing_opt_in' => true,
                'marketing_consented_at' => now(),
                'marketing_unsubscribe_token' => $user->marketing_unsubscribe_token ?: Str::random(64),
            ])->save();
        }

        $request->session()->put('guest_discount_pending_user_id', $user->id);

        if (! $this->issueOtp($user)) {
            return response()->json([
                'message' => 'We could not send the code. You can continue booking at the regular price or try again.',
            ], 503);
        }

        return response()->json([
            'message' => 'A 4-digit code was sent to '.$this->maskEmail($user->email).'.',
            'email' => $this->maskEmail($user->email),
            'attempts' => self::MAX_OTP_ATTEMPTS,
        ]);
    }

    public function verifyCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'digits:4'],
        ]);

        $user = User::find($request->session()->get('guest_discount_pending_user_id'));
        if (! $user?->isGuest()) {
            return response()->json([
                'message' => 'Request a new verification code.',
            ], 422);
        }

        if (! $user->email_otp_hash || ! $user->email_otp_expires_at || $user->email_otp_expires_at->isPast()) {
            return response()->json([
                'message' => 'This code has expired. Request a new one.',
            ], 422);
        }

        if ($user->email_otp_attempts >= self::MAX_OTP_ATTEMPTS) {
            return response()->json([
                'message' => 'Three attempts were used. Request a new code to try again.',
                'locked' => true,
            ], 422);
        }

        if (! Hash::check($validated['code'], $user->email_otp_hash)) {
            $user->increment('email_otp_attempts');
            $remaining = max(0, self::MAX_OTP_ATTEMPTS - $user->fresh()->email_otp_attempts);

            return response()->json([
                'message' => $remaining > 0
                    ? 'Incorrect code. '.$remaining.' attempt'.($remaining === 1 ? '' : 's').' remaining.'
                    : 'Three attempts were used. Request a new code to try again.',
                'attempts_remaining' => $remaining,
                'locked' => $remaining === 0,
            ], 422);
        }

        $this->completeVerification($request, $user);

        return response()->json([
            'message' => 'Discount unlocked. Updating your room prices…',
            'discount_unlocked' => true,
        ]);
    }

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
                $request->session()->put('guest_discount_unlocked_user_id', $user->id);

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
        if ($user->email_otp_attempts >= self::MAX_OTP_ATTEMPTS) {
            return back()->withErrors(['code' => 'Three attempts were used. Request a new code.']);
        }
        if (! Hash::check($validated['code'], $user->email_otp_hash)) {
            $user->increment('email_otp_attempts');

            return back()->withErrors(['code' => 'The code is incorrect.']);
        }

        $this->completeVerification($request, $user);

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

    private function completeVerification(Request $request, User $user): void
    {
        $isNewVisit = (int) $request->session()->get('guest_discount_unlocked_user_id') !== (int) $user->id;

        $user->forceFill([
            'email_verified_at' => $user->email_verified_at ?? now(),
            'email_otp_hash' => null,
            'email_otp_expires_at' => null,
            'email_otp_attempts' => 0,
            'discount_unlock_count' => (int) $user->discount_unlock_count + ($isNewVisit ? 1 : 0),
            'last_discount_unlocked_at' => now(),
        ])->save();

        GuestBookingRequest::query()
            ->whereNull('user_id')
            ->whereRaw('LOWER(guest_email) = ?', [strtolower($user->email)])
            ->update(['user_id' => $user->id]);

        // Persist unlock before login so session regenerate keeps the flag.
        $request->session()->put('guest_discount_unlocked_user_id', $user->id);
        $request->session()->forget('guest_discount_pending_user_id');
        Auth::login($user, true);
        $request->session()->put('guest_discount_unlocked_user_id', $user->id);
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');
        $visible = substr($local, 0, min(2, strlen($local)));

        return $visible.str_repeat('•', max(2, strlen($local) - strlen($visible))).'@'.$domain;
    }
}
