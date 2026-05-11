<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\ReservationSourceSetting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class OnlineReservationController extends Controller
{
    private const ACCEPTED_STATUSES = ['confirmed', 'checked_in', 'checked_out'];

    private const DECLINED_STATUSES = ['cancelled', 'no_show'];

    public function index(Request $request)
    {
        $filters = $request->validate([
            'guest' => ['nullable', 'string', 'max:255'],
            'source_id' => ['nullable', 'integer', 'exists:reservation_source_settings,id'],
            'arrival_from' => ['nullable', 'date'],
            'arrival_to' => ['nullable', 'date', 'after_or_equal:arrival_from'],
        ]);

        $websiteSources = $this->websiteSources();
        $baseQuery = $this->websiteReservationsQuery($filters);

        $pendingQuery = (clone $baseQuery)->where('status', 'pending');
        $acceptedQuery = (clone $baseQuery)->whereIn('status', self::ACCEPTED_STATUSES);
        $declinedQuery = (clone $baseQuery)->whereIn('status', self::DECLINED_STATUSES);
        $unassignedWebsiteQuery = (clone $baseQuery)->whereNull('source_id')->whereNull('created_by');

        $pendingCount = (clone $pendingQuery)->count();
        $acceptedCount = (clone $acceptedQuery)->count();
        $declinedCount = (clone $declinedQuery)->count();
        $totalWebsiteReservations = (clone $baseQuery)->count();
        $unassignedWebsiteCount = (clone $unassignedWebsiteQuery)->count();
        $queueValue = (float) (clone $pendingQuery)->sum('grand_total');
        $latestWebsiteBookingAt = optional((clone $baseQuery)->latest('created_at')->first())->created_at;
        $canEditReservations = (bool) optional($request->user())->can('reservation.edit');

        $pendingReservations = $this->formatReservations(
            (clone $pendingQuery)->latest('id')->limit(50)->get(),
            $canEditReservations
        );
        $acceptedReservations = $this->formatReservations(
            (clone $acceptedQuery)->latest('id')->limit(50)->get(),
            $canEditReservations
        );
        $declinedReservations = $this->formatReservations(
            (clone $declinedQuery)->latest('id')->limit(50)->get(),
            $canEditReservations
        );

        $sourceLookup = $websiteSources->keyBy('id');
        $sourceSnapshots = (clone $baseQuery)
            ->selectRaw('source_id, COUNT(*) as reservation_count')
            ->groupBy('source_id')
            ->orderByDesc('reservation_count')
            ->get()
            ->map(function ($row) use ($sourceLookup) {
                $sourceId = $row->source_id !== null ? (int) $row->source_id : null;
                $source = $sourceId !== null ? $sourceLookup->get($sourceId) : null;
                $label = $sourceId !== null
                    ? $this->sourceLabel($source)
                    : $this->unassignedWebsiteSourceLabel();

                return [
                    'id' => $sourceId,
                    'name' => $label,
                    'count' => (int) $row->reservation_count,
                    'color' => $this->sourceColor($label.'|'.($sourceId ?? 'unassigned-website')),
                ];
            })
            ->values();

        return view('admin.online_reservation.index', [
            'filters' => $filters,
            'filtersOpen' => collect($filters)->filter(fn ($value) => filled($value))->isNotEmpty(),
            'websiteSources' => $websiteSources,
            'sourceSnapshots' => $sourceSnapshots,
            'pendingReservations' => $pendingReservations,
            'acceptedReservations' => $acceptedReservations,
            'declinedReservations' => $declinedReservations,
            'pendingCount' => $pendingCount,
            'acceptedCount' => $acceptedCount,
            'declinedCount' => $declinedCount,
            'totalWebsiteReservations' => $totalWebsiteReservations,
            'queueValue' => $queueValue,
            'activeSourceCount' => $sourceSnapshots->count(),
            'latestWebsiteBookingAt' => $latestWebsiteBookingAt,
            'hasWebsiteSources' => $websiteSources->isNotEmpty(),
            'hasUnassignedWebsiteReservations' => $unassignedWebsiteCount > 0,
        ]);
    }

    private function websiteReservationsQuery(array $filters): Builder
    {
        $query = Reservation::query()
            ->with(['guest', 'unit.unitType', 'source.masterSource', 'invoice'])
            ->where(function (Builder $reservationQuery) {
                $reservationQuery
                    ->whereHas('source', function (Builder $sourceQuery) {
                        $this->applyWebsiteSourceConstraint($sourceQuery);
                    })
                    ->orWhere(function (Builder $fallbackQuery) {
                        $this->applyWebsiteFallbackConstraint($fallbackQuery);
                    });
            });

        $guest = trim((string) ($filters['guest'] ?? ''));
        if ($guest !== '') {
            $query->where(function (Builder $reservationQuery) use ($guest) {
                $reservationQuery
                    ->where('reservation_number', 'like', '%'.$guest.'%')
                    ->orWhereHas('guest', function (Builder $guestQuery) use ($guest) {
                        $guestQuery
                            ->where('first_name', 'like', '%'.$guest.'%')
                            ->orWhere('second_name', 'like', '%'.$guest.'%')
                            ->orWhere('middle_name', 'like', '%'.$guest.'%')
                            ->orWhere('last_name', 'like', '%'.$guest.'%');
                    });
            });
        }

        if (filled($filters['source_id'] ?? null)) {
            $query->where('source_id', (int) $filters['source_id']);
        }

        if (filled($filters['arrival_from'] ?? null)) {
            $query->whereDate('check_in_date', '>=', $filters['arrival_from']);
        }

        if (filled($filters['arrival_to'] ?? null)) {
            $query->whereDate('check_in_date', '<=', $filters['arrival_to']);
        }

        return $query;
    }

    private function websiteSources(): Collection
    {
        return ReservationSourceSetting::query()
            ->with('masterSource')
            ->where(function (Builder $query) {
                $this->applyWebsiteSourceConstraint($query);
            })
            ->orderBy('report_name')
            ->orderBy('id')
            ->get();
    }

    private function applyWebsiteSourceConstraint(Builder $query): void
    {
        $query->where(function (Builder $sourceQuery) {
            $sourceQuery
                ->where('report_name', 'like', '%website%')
                ->orWhereHas('masterSource', function (Builder $masterSourceQuery) {
                    $masterSourceQuery->where('name', 'like', '%Website%');
                });
        });
    }

    private function applyWebsiteFallbackConstraint(Builder $query): void
    {
        $query
            ->whereNull('source_id')
            ->whereNull('created_by');
    }

    private function formatReservations(Collection $reservations, bool $canEditReservations): Collection
    {
        return $reservations->map(function (Reservation $reservation) use ($canEditReservations) {
            $guestName = $reservation->guest?->full_name ?: 'Guest';
            $amount = (float) ($reservation->grand_total ?? $reservation->invoice?->total ?? 0);

            return [
                'id' => $reservation->id,
                'reservation_number' => $reservation->reservation_number,
                'guest_name' => $guestName,
                'guest_initials' => $this->initials($guestName),
                'guest_sub' => $reservation->guest?->mobile ?: $this->unitLabel($reservation),
                'stay_from' => optional($reservation->check_in_date)->format('d M Y'),
                'stay_to' => optional($reservation->check_out_date)->format('d M Y'),
                'stay_note' => optional($reservation->created_at)->format('d M Y, h:i A'),
                'nights' => (int) ($reservation->nights ?? 0),
                'unit_label' => $this->unitLabel($reservation),
                'source_name' => $this->sourceLabel($reservation->source),
                'source_color' => $this->sourceColor(($reservation->source_id ?? 'website').'|'.$reservation->reservation_number),
                'amount' => $amount,
                'amount_note' => $reservation->invoice?->status
                    ? Str::headline($reservation->invoice->status).' invoice'
                    : 'Website booking record',
                'status_label' => $this->statusLabel($reservation->status),
                'status_bucket' => $this->statusBucket($reservation->status),
                'edit_url' => $canEditReservations
                    ? route('dashboard.reservation.edit', $reservation->id)
                    : null,
            ];
        })->values();
    }

    private function unitLabel(Reservation $reservation): string
    {
        $unitNumber = $reservation->unit?->unit_number;
        $unitType = $reservation->unit?->unitType?->name;

        if ($unitNumber && $unitType) {
            return $unitType.' - Unit '.$unitNumber;
        }

        if ($unitNumber) {
            return 'Unit '.$unitNumber;
        }

        if ($unitType) {
            return $unitType;
        }

        return 'Unit not assigned';
    }

    private function sourceLabel(?ReservationSourceSetting $source): string
    {
        return trim((string) ($source?->report_name ?: $source?->masterSource?->name ?: $this->unassignedWebsiteSourceLabel()));
    }

    private function unassignedWebsiteSourceLabel(): string
    {
        return 'Direct Website';
    }

    private function sourceColor(string $seed): string
    {
        $palette = ['#1d4ed8', '#0f766e', '#b45309', '#7c3aed', '#be123c', '#0369a1'];
        $index = abs(crc32($seed)) % count($palette);

        return $palette[$index];
    }

    private function statusBucket(?string $status): string
    {
        return match ($status) {
            'pending' => 'pending',
            'confirmed', 'checked_in', 'checked_out' => 'accepted',
            'cancelled', 'no_show' => 'declined',
            default => 'pending',
        };
    }

    private function statusLabel(?string $status): string
    {
        return match ($status) {
            'pending' => __('dashboard.pending'),
            'confirmed' => __('dashboard.confirmed'),
            'checked_in' => __('dashboard.checked_in'),
            'checked_out' => __('dashboard.checked_out'),
            'cancelled' => __('dashboard.cancelled'),
            'no_show' => __('dashboard.no_show'),
            default => Str::of((string) $status)->replace('_', ' ')->title()->value(),
        };
    }

    private function initials(string $name): string
    {
        $parts = collect(preg_split('/\s+/', trim($name)) ?: [])
            ->filter()
            ->take(2);

        if ($parts->isEmpty()) {
            return 'GS';
        }

        return $parts
            ->map(fn ($part) => mb_strtoupper(mb_substr((string) $part, 0, 1)))
            ->implode('');
    }
}
