<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenantByDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $baseDomain = $this->getBaseDomain();

        if ($this->isMainDomain($host, $baseDomain)) {
            return $next($request);
        }

        $subdomain = $this->extractSubdomain($host, $baseDomain);

        if ($subdomain === null) {
            return $next($request);
        }

        $tenant = Tenant::where('subdomain', $subdomain)->first();

        if (! $tenant) {
            abort(404, 'Tenant not found');
        }

        app(TenantContext::class)->setTenantId($tenant->id);

        return $next($request);
    }

    protected function getBaseDomain(): string
    {
        $url = config('app.url');

        $host = parse_url($url, PHP_URL_HOST);

        return $host ?: 'localhost';
    }

    protected function isMainDomain(string $host, string $baseDomain): bool
    {
        return $host === $baseDomain
            || $host === 'www.'.$baseDomain
            || $host === 'localhost'
            || filter_var($host, FILTER_VALIDATE_IP) !== false;
    }

    protected function extractSubdomain(string $host, string $baseDomain): ?string
    {
        $host = strtolower($host);
        $baseDomain = strtolower($baseDomain);
        $candidateBaseDomains = array_values(array_unique(array_filter([
            $baseDomain,
            'localhost',
        ])));

        foreach ($candidateBaseDomains as $candidateBaseDomain) {
            $suffix = '.'.$candidateBaseDomain;

            if (! str_ends_with($host, $suffix)) {
                continue;
            }

            $prefix = substr($host, 0, -strlen($suffix));

            if ($prefix === '' || $prefix === 'www') {
                return null;
            }

            return $prefix;
        }

        return null;
    }
}
