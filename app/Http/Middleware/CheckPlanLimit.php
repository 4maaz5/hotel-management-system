<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanLimit
{
    public function handle(Request $request, Closure $next, string $limitKey): Response
    {
        $tenant = $this->resolveTenant();

        if ($tenant && ! $tenant->canExceedLimit($limitKey)) {
            abort(403, "You have reached the limit for: ".$limitKey);
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
