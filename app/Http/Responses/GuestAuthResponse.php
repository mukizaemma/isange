<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\RegisterResponse;
use Symfony\Component\HttpFoundation\Response;

class GuestAuthResponse implements LoginResponse, RegisterResponse
{
    public function toResponse($request): Response
    {
        if ($request->wantsJson()) {
            return new JsonResponse('', 204);
        }

        $user = $request->user();
        if ($user?->isGuest()) {
            return redirect()->route(
                $user->hasUnlockedDiscount() ? 'booking.checkout' : 'guest.discount'
            );
        }

        return redirect()->intended('/dashboard');
    }
}
