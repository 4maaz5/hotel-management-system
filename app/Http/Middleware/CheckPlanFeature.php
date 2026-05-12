<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanFeature
{
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $tenant = $this->resolveTenant();

        if (! $tenant || ! $tenant->hasFeature($feature)) {
            abort(403, "Your subscription plan does not include: ".$feature);
        }

        return $next($request);
    }

    protected function resolveTenant(): ?Tenant
    {
        $id = app(TenantContext::class)->id();

        if (! $id) {
            return null;
        }

        return Tenant::with('plan')->find($id);
    }
}
