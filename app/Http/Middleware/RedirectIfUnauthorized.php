<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfUnauthorized
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->role === 'manager') {
            // Exclude scan page AND logout route
            if (
                ! $request->routeIs('employee.card.scan') &&
                ! $request->routeIs('dashboard.logout')
            ) {
                return redirect()->route('employee.card.scan')
                    ->with('delete', 'You are only allowed to access your scan page.');
            }
        }

        return $next($request);
    }
}
