<?php

namespace App\Http\Middleware;

use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantSubscription
{
    public function handle(Request $request, Closure $next, string $guard = 'web'): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            app(TenantContext::class)->forget();

            return $next($request);
        }

        $tenant = $user->tenant;

        if (! $tenant || ! $tenant->isActiveSubscription()) {
            Auth::guard($guard)->logout();

            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            return redirect()->route('login')
                ->withErrors([
                    'email' => 'Your tenant subscription is inactive, suspended, or expired.',
                ]);
        }

        app(TenantContext::class)->setTenant($tenant);

        return $next($request);
    }
}
