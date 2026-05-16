<?php

namespace App\Services\Chatbot;

use App\Models\Reservation;
use App\Models\Unit;
use App\Models\UnitTypeCustomization;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AvailabilityToolService
{
    public function __construct(
        private readonly ReservationPricingService $pricingService,
    ) {
    }

    public function checkAvailability(array $parameters): array
    {
        $checkIn = $this->parseDate($parameters['check_in_date'] ?? '');
        $checkOut = $this->parseDate($parameters['check_out_date'] ?? '');
        $roomType = trim((string) ($parameters['room_type'] ?? ''));
        $reservationType = ($parameters['reservation_type'] ?? '') === 'monthly' ? 'monthly' : 'daily';
        $adults = max(1, (int) ($parameters['adults'] ?? 1));
        $children = max(0, (int) ($parameters['children'] ?? 0));
        $guestCount = $adults + $children;
        $publicWebsiteOnly = (bool) ($parameters['public_website_only'] ?? false);
        $branchId = isset($parameters['branch_id']) ? (int) $parameters['branch_id'] : null;

        $missing = [];

        if (! $checkIn) {
            $missing[] = 'check_in_date';
        }

        if (! $checkOut) {
            $missing[] = 'check_out_date';
        }

        if ($missing !== []) {
            return [
                'status' => 'needs_more_info',
                'missing_fields' => $missing,
                'message' => 'Check-in and check-out dates are required.',
            ];
        }

        if ($checkOut->lessThanOrEqualTo($checkIn)) {
            return [
                'status' => 'failed',
                'message' => 'Check-out date must be after check-in date.',
            ];
        }

        $cacheKey = 'chatbot:availability:'.md5(json_encode([
            $checkIn->toDateString(),
            $checkOut->toDateString(),
            $roomType,
            $reservationType,
            $adults,
            $children,
            $publicWebsiteOnly,
            $branchId,
        ]));

        return Cache::remember($cacheKey, (int) config('chatbot.availability_cache_ttl', 120), function () use ($checkIn, $checkOut, $roomType, $reservationType, $guestCount, $adults, $children, $publicWebsiteOnly, $branchId) {
            $publishedRoomTypes = $publicWebsiteOnly ? $this->publishedWebsiteRoomTypes() : collect();

            $query = Unit::query()
                ->when($branchId, fn ($unitQuery) => $unitQuery->where('branch_id', $branchId))
                ->where('is_active', true)
                ->with(['unitType', 'floor', 'block']);

            if ($publicWebsiteOnly) {
                $publishedUnitTypeIds = $publishedRoomTypes->pluck('unit_type_id')->filter()->unique()->values();

                if ($publishedUnitTypeIds->isEmpty()) {
                    return [
                        'status' => 'completed',
                        'message' => 'No units are available for the selected dates.',
                        'check_in_date' => $checkIn->toDateString(),
                        'check_out_date' => $checkOut->toDateString(),
                        'reservation_type' => $reservationType,
                        'adults' => $adults,
                        'children' => $children,
                        'available_count' => 0,
                        'units' => [],
                        'upsell_suggestions' => [],
                    ];
                }

                $query->whereIn('unit_type_id', $publishedUnitTypeIds->all());
            }

            if ($guestCount > 0) {
                $query->where(function ($unitQuery) use ($guestCount) {
                    $unitQuery->whereNull('base_occupancy')
                        ->orWhere('base_occupancy', '>=', $guestCount);
                });
            }

            if ($roomType !== '') {
                if ($publicWebsiteOnly) {
                    $matchingUnitTypeIds = $publishedRoomTypes
                        ->filter(function (UnitTypeCustomization $customization) use ($roomType) {
                            return $this->matchesWebsiteRoomType($customization, $roomType);
                        })
                        ->pluck('unit_type_id')
                        ->filter()
                        ->unique()
                        ->values();

                    if ($matchingUnitTypeIds->isEmpty()) {
                        return [
                            'status' => 'completed',
                            'message' => 'No units are available for the selected dates.',
                            'check_in_date' => $checkIn->toDateString(),
                            'check_out_date' => $checkOut->toDateString(),
                            'reservation_type' => $reservationType,
                            'adults' => $adults,
                            'children' => $children,
                            'available_count' => 0,
                            'units' => [],
                            'upsell_suggestions' => [],
                        ];
                    }

                    $query->whereIn('unit_type_id', $matchingUnitTypeIds->all());
                } else {
                    $query->whereHas('unitType', function ($unitTypeQuery) use ($roomType) {
                        $unitTypeQuery->where('name', 'like', '%'.$roomType.'%');
                    });
                }
            }

            $units = $query->get();

            $bookedUnitIds = Reservation::query()
                ->when($branchId, fn ($reservationQuery) => $reservationQuery->where('branch_id', $branchId))
                ->whereNotIn('status', ['cancelled', 'checked_out', 'no_show'])
                ->where('check_in_date', '<', $checkOut->toDateString())
                ->where('check_out_date', '>', $checkIn->toDateString())
                ->pluck('unit_id');

            $availableUnits = $units
                ->reject(fn (Unit $unit) => $bookedUnitIds->contains($unit->id))
                ->values();

            $unitsData = $availableUnits->map(function (Unit $unit) use ($checkIn, $checkOut, $reservationType, $publicWebsiteOnly, $publishedRoomTypes) {
                return [
                    'unit_id' => $unit->id,
                    'unit_number' => $unit->unit_number,
                    'room_type' => $publicWebsiteOnly
                        ? $this->websiteRoomTypeLabel($unit, $publishedRoomTypes)
                        : ($unit->unitType?->name ?? ''),
                    'floor' => $unit->floor?->name,
                    'block' => $unit->block?->name,
                    'base_occupancy' => $unit->base_occupancy,
                    'starting_rate' => $this->pricingService->startingRate($unit, $checkIn, $checkOut, $reservationType),
                ];
            })->values();

            return [
                'status' => 'completed',
                'message' => $availableUnits->isEmpty()
                    ? 'No units are available for the selected dates.'
                    : 'Availability found.',
                'check_in_date' => $checkIn->toDateString(),
                'check_out_date' => $checkOut->toDateString(),
                'reservation_type' => $reservationType,
                'adults' => $adults,
                'children' => $children,
                'available_count' => $availableUnits->count(),
                'units' => $unitsData->take(6)->all(),
                'upsell_suggestions' => $this->upsellSuggestions($unitsData, $roomType),
            ];
        });
    }

    public function firstMatchingUnit(array $parameters): ?Unit
    {
        $availability = $this->checkAvailability($parameters);

        if (($availability['status'] ?? null) !== 'completed' || empty($availability['units'])) {
            return null;
        }

        $unitId = $availability['units'][0]['unit_id'] ?? null;

        return $unitId ? Unit::query()->with('unitType')->find($unitId) : null;
    }

    private function parseDate(string $value): ?Carbon
    {
        if (trim($value) === '') {
            return null;
        }

        try {
            return Carbon::parse($value)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }

    private function upsellSuggestions(Collection $units, string $requestedRoomType): array
    {
        $suggestions = $units
            ->groupBy(fn (array $unit) => $unit['room_type'] ?: 'Other')
            ->map(function (Collection $group, string $roomType) {
                return [
                    'room_type' => $roomType,
                    'available_count' => $group->count(),
                    'starting_rate' => round((float) $group->min('starting_rate'), 2),
                ];
            })
            ->sortBy('starting_rate')
            ->values();

        if ($requestedRoomType !== '') {
            $suggestions = $suggestions->reject(function (array $item) use ($requestedRoomType) {
                return Str::contains(Str::lower($item['room_type']), Str::lower($requestedRoomType));
            })->values();
        }

        return $suggestions->take(2)->all();
    }

    private function publishedWebsiteRoomTypes(): Collection
    {
        return UnitTypeCustomization::query()
            ->where('is_published_online', true)
            ->get(['unit_type_id', 'name', 'website_name_en', 'website_name_ar']);
    }

    private function matchesWebsiteRoomType(UnitTypeCustomization $customization, string $roomType): bool
    {
        $needle = Str::lower(trim($roomType));

        if ($needle === '') {
            return true;
        }

        return collect([
            $customization->name,
            $customization->website_name_en,
            $customization->website_name_ar,
        ])->filter()->contains(function ($candidate) use ($needle) {
            return Str::contains(Str::lower((string) $candidate), $needle);
        });
    }

    private function websiteRoomTypeLabel(Unit $unit, Collection $publishedRoomTypes): string
    {
        /** @var UnitTypeCustomization|null $customization */
        $customization = $publishedRoomTypes->firstWhere('unit_type_id', $unit->unit_type_id);

        return $customization?->website_name_en
            ?: $customization?->website_name_ar
            ?: $customization?->name
            ?: ($unit->unitType?->name ?? '');
    }
}
