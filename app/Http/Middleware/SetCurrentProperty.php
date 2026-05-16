<?php

namespace App\Http\Middleware;

use App\Models\Property;
use App\Support\PropertyBranchManager;
use App\Support\PropertyContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentProperty
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $routeName = $request->route()?->getName();
        $isSuperAdminRoute = $routeName && str_starts_with($routeName, 'super-admin.');

        if (! $isSuperAdminRoute && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            app(PropertyContext::class)->forget();

            return $next($request);
        }

        $selectedProperty = null;
        $selectedPropertyId = $request->session()->get('current_property_id')
            ?: $request->session()->get('property_id');
        $selectedBranchId = $request->session()->get('branch_id');

        if ($selectedPropertyId && $user->canAccessProperty((int) $selectedPropertyId)) {
            $selectedProperty = $user->accessiblePropertiesQuery()
                ->find($selectedPropertyId);
        }

        if (! $selectedProperty && $selectedBranchId) {
            $selectedProperty = $user->accessiblePropertiesQuery()
                ->where('branch_id', (int) $selectedBranchId)
                ->first();
        }

        if (! $selectedProperty && $user->branch_id) {
            $selectedProperty = $user->accessiblePropertiesQuery()
                ->where('branch_id', $user->branch_id)
                ->first();
        }

        if (! $selectedProperty) {
            $selectedProperty = $user->accessiblePropertiesQuery()
                ->orderBy('property_name_en')
                ->first();
        }

        if ($selectedProperty) {
            $selectedProperty = app(PropertyBranchManager::class)->ensureBranch($selectedProperty, $user);
            $request->session()->put('current_property_id', $selectedProperty->id);
            $request->session()->forget('property_id');
            $request->session()->put('branch_id', $selectedProperty->branch_id);
            app(PropertyContext::class)->setProperty($selectedProperty);
        } else {
            $request->session()->forget('current_property_id');
            $request->session()->forget('property_id');
            $request->session()->forget('branch_id');
            app(PropertyContext::class)->forget();
        }

        return $next($request);
    }
}
