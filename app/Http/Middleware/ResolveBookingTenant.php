<?php

namespace App\Http\Middleware;

use App\Support\BookingContextResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveBookingTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $property = app(BookingContextResolver::class)->resolveAndApply($request);

        if (! $property) {
            $refererQuery = [];
            $referer = (string) $request->headers->get('referer', '');

            if ($referer !== '') {
                parse_str((string) parse_url($referer, PHP_URL_QUERY), $refererQuery);
            }

            $propertyQuery = array_filter([
                'property_code' => $refererQuery['property_code'] ?? null,
                'property_id' => $refererQuery['property_id'] ?? null,
            ]);
            $propertyQuery = $propertyQuery ?: (array) $request->session()->get('booking_property_query', []);

            $requestHasProperty = $request->hasAny(['property_code', 'property_id']);
            if ($request->is('book/*') && ! $requestHasProperty && $propertyQuery !== []) {
                return redirect()->to($request->url().'?'.http_build_query($propertyQuery));
            }

            if ($request->is('book/*')) {
                return redirect()->route('booking.rooms.index');
            }

            abort(404);
        }

        return $next($request);
    }
}
