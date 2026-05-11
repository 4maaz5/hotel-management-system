<?php

namespace App\Http\Middleware;

use App\Models\MobileApiToken;
use App\Support\PropertyContext;
use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateMobileApi
{
    public function handle(Request $request, Closure $next): Response
    {
        app(TenantContext::class)->forget();
        app(PropertyContext::class)->forget();

        $plainToken = $request->bearerToken();

        if (! $plainToken) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $token = MobileApiToken::with('user')
            ->where('token_hash', hash('sha256', $plainToken))
            ->whereNull('revoked_at')
            ->where(function ($query): void {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->first();

        if (! $token || ! $token->user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $user = $token->user;

        if ($user->status && strtolower($user->status) !== 'active') {
            return response()->json(['message' => 'Your account is inactive.'], 403);
        }

        Auth::setUser($user);
        $request->setUserResolver(fn () => $user);

        app(TenantContext::class)->setTenantId($user->company_id);

        $propertyId = $token->property?->id;
        if (! $propertyId && $user->branch_id) {
            $property = \App\Models\Property::where('branch_id', $user->branch_id)->first();
            $propertyId = $property?->id;
        }
        if ($propertyId && $user->canAccessProperty((int) $propertyId)) {
            app(PropertyContext::class)->setPropertyId((int) $propertyId);
        }

        $token->forceFill(['last_used_at' => now()])->save();

        return $next($request);
    }
}
