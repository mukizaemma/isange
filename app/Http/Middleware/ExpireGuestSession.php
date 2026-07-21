<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ExpireGuestSession
{
    public const LIFETIME_MINUTES = 120;

    public function handle(Request $request, Closure $next): Response
    {
        // Reset request-scoped config in long-running workers; guest requests
        // opt into browser-close expiry below.
        config(['session.expire_on_close' => false]);

        $user = $request->user();
        if ($user?->isGuest()) {
            $this->configureGuestSession($request);

            $expiresAt = (int) $request->session()->get('guest_session_expires_at', 0);
            if ($expiresAt > 0 && $expiresAt <= now()->timestamp) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
        }

        $response = $next($request);

        // A controller may log the guest in during this request. Configure the
        // cookie before StartSession adds it to the outgoing response.
        if ($request->user()?->isGuest()) {
            $this->configureGuestSession($request);
        }

        return $response;
    }

    private function configureGuestSession(Request $request): void
    {
        // Guest cookies disappear when the browser closes; staff/admin sessions
        // retain the application's normal session behavior.
        config(['session.expire_on_close' => true]);

        if (! $request->session()->has('guest_session_expires_at')) {
            $request->session()->put(
                'guest_session_expires_at',
                now()->addMinutes(self::LIFETIME_MINUTES)->timestamp
            );
        }
    }
}
