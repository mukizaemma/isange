<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->canManageUsers()) {
            return $next($request);
        }

        return redirect()->route('dashboard')->with('error', 'You are not authorized to manage users.');
    }
}
