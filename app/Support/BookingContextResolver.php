<?php

namespace App\Support;

use App\Models\Company;
use App\Models\Property;
use App\Models\Scopes\TenantScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class BookingContextResolver
{
    public function resolveAndApply(Request $request): ?Property
    {
        $this->forget();

        $property = $this->resolveProperty($request);

        if (! $property) {
            return null;
        }

        $tenant = Company::find($property->company_id);

        if (! $tenant || ! $tenant->isActiveSubscription()) {
            return null;
        }

        app(TenantContext::class)->setTenant($tenant);
        app(PropertyContext::class)->setProperty($property);
        session([
            'booking_property_query' => filled($property->property_code)
                ? ['property_code' => $property->property_code]
                : ['property_id' => $property->id],
        ]);

        return $property;
    }

    public function resolveProperty(Request $request): ?Property
    {
        if (! Schema::hasTable('properties')) {
            return null;
        }

        $properties = Property::withoutGlobalScope(TenantScope::class)
            ->with(['mainPhoto', 'photos', 'commercialDetail'])
            ->get();

        $propertyIdentifier = $request->query('property_id') ?: $request->query('property_code');

        if ($propertyIdentifier) {
            $matchedByIdentifier = $properties->first(function (Property $property) use ($propertyIdentifier) {
                return (string) $property->id === (string) $propertyIdentifier
                    || strcasecmp((string) $property->property_code, (string) $propertyIdentifier) === 0;
            });

            if ($matchedByIdentifier) {
                return $matchedByIdentifier;
            }
        }

        $host = $this->normalizeHost((string) $request->getHost());

        if ($host !== '') {
            $matchedByHost = $properties->first(function (Property $property) use ($host) {
                $websiteHost = $this->normalizeHost((string) $property->website);

                return $websiteHost !== '' && in_array($host, $this->hostCandidates($websiteHost), true);
            });

            if ($matchedByHost) {
                return $matchedByHost;
            }
        }

        return $properties->count() === 1 ? $properties->first() : null;
    }

    public function forget(): void
    {
        app(TenantContext::class)->forget();
        app(PropertyContext::class)->forget();
    }

    private function normalizeHost(string $hostOrUrl): string
    {
        $hostOrUrl = trim(strtolower($hostOrUrl));

        if ($hostOrUrl === '') {
            return '';
        }

        $host = parse_url($hostOrUrl, PHP_URL_HOST) ?: $hostOrUrl;
        $host = preg_replace('/:\d+$/', '', trim((string) $host));

        return preg_replace('/^www\./', '', $host) ?: '';
    }

    private function hostCandidates(string $host): array
    {
        $candidates = [$host];

        if (str_ends_with($host, '.localhost')) {
            $candidates[] = substr($host, 0, -strlen('.localhost'));
        } elseif (! str_contains($host, '.')) {
            $candidates[] = $host.'.localhost';
        }

        return array_values(array_unique(array_filter($candidates)));
    }
}
