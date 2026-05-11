<?php

namespace App\Services\Chatbot;

use App\Models\HighWeekday;
use App\Models\SeasonalRate;
use App\Models\SpecialRate;
use App\Models\TaxFeeCustomization;
use App\Models\Unit;
use App\Models\UnitCustomRate;
use App\Models\UnitTypeRate;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class ReservationPricingService
{
    public function quote(
        Unit $unit,
        CarbonInterface $checkIn,
        CarbonInterface $checkOut,
        string $reservationType = 'daily',
        float $discount = 0,
        float $securityDeposit = 0,
        float $paidAmount = 0
    ): array {
        $nights = max(1, $checkIn->diffInDays($checkOut));
        $reservationType = $reservationType === 'monthly' ? 'monthly' : 'daily';

        if ($reservationType === 'monthly') {
            $monthlyRate = $this->monthlyRate($unit, $checkIn);
            $months = max(1, $checkIn->diffInMonths($checkOut));
            $totalRent = round($monthlyRate['rate'] * $months, 2);
            $dailyRate = $months > 0 ? round($totalRent / max(1, $nights), 2) : 0.0;
            $breakdown = [[
                'period' => "{$months} month(s)",
                'rate' => round($monthlyRate['rate'], 2),
                'source' => $monthlyRate['source'],
            ]];
        } else {
            $breakdown = [];
            $totalRent = 0;

            for ($cursor = Carbon::parse($checkIn); $cursor->lt($checkOut); $cursor->addDay()) {
                $rate = $this->dailyRateForDate($unit, $cursor);
                $totalRent += $rate['rate'];
                $breakdown[] = [
                    'date' => $cursor->toDateString(),
                    'rate' => round($rate['rate'], 2),
                    'source' => $rate['source'],
                    'high_weekday' => $rate['high_weekday'],
                ];
            }

            $dailyRate = round($totalRent / max(1, $nights), 2);
            $monthlyRate = ['rate' => 0.0, 'source' => 'not_applicable'];
        }

        $totalRent = round($totalRent, 2);
        $totalTaxes = $this->calculateTaxes($totalRent, $nights, $checkIn);
        $grandTotal = round($totalRent - $discount + $totalTaxes, 2);
        $balance = round($grandTotal - $paidAmount - $securityDeposit, 2);

        return [
            'reservation_type' => $reservationType,
            'nights' => $nights,
            'daily_rate' => $dailyRate,
            'monthly_rate' => round($monthlyRate['rate'], 2),
            'total_rent' => $totalRent,
            'subtotal' => $totalRent,
            'discount' => round($discount, 2),
            'total_taxes_fees' => $totalTaxes,
            'security_deposit' => round($securityDeposit, 2),
            'paid_amount' => round($paidAmount, 2),
            'grand_total' => $grandTotal,
            'balance' => $balance,
            'breakdown' => $breakdown,
        ];
    }

    public function startingRate(Unit $unit, CarbonInterface $checkIn, CarbonInterface $checkOut, string $reservationType = 'daily'): float
    {
        $quote = $this->quote($unit, $checkIn, $checkOut, $reservationType);

        return $reservationType === 'monthly'
            ? (float) $quote['monthly_rate']
            : (float) $quote['daily_rate'];
    }

    private function dailyRateForDate(Unit $unit, CarbonInterface $date): array
    {
        $date = Carbon::parse($date);
        $unitTypeId = $unit->rate_unit_type_id ?: $unit->unit_type_id;
        $highWeekday = $this->isHighWeekday($date);

        $unitCustomRate = UnitCustomRate::query()->where('unit_id', $unit->id)->first();

        if ($unitCustomRate) {
            return [
                'rate' => (float) ($highWeekday ? $unitCustomRate->high_weekday_rate : $unitCustomRate->low_weekday_rate),
                'source' => 'unit_custom_rate',
                'high_weekday' => $highWeekday,
            ];
        }

        $specialRate = SpecialRate::query()
            ->where('is_active', true)
            ->whereDate('start_date', '<=', $date->toDateString())
            ->whereDate('end_date', '>=', $date->toDateString())
            ->with(['unitRates' => fn ($query) => $query->where('unit_type_id', $unitTypeId)])
            ->orderByDesc('id')
            ->first();

        if ($specialRate && $specialRate->unitRates->isNotEmpty()) {
            return [
                'rate' => (float) ($specialRate->unitRates->first()->rate ?? 0),
                'source' => 'special_rate',
                'high_weekday' => $highWeekday,
            ];
        }

        $seasonalRate = SeasonalRate::query()
            ->where('is_active', true)
            ->whereDate('start_date', '<=', $date->toDateString())
            ->whereDate('end_date', '>=', $date->toDateString())
            ->with(['unitRates' => fn ($query) => $query->where('unit_type_id', $unitTypeId)])
            ->orderByDesc('id')
            ->first();

        if ($seasonalRate && $seasonalRate->unitRates->isNotEmpty()) {
            $seasonalUnitRate = $seasonalRate->unitRates->first();

            return [
                'rate' => (float) ($highWeekday ? $seasonalUnitRate->high_weekday_rate : $seasonalUnitRate->low_weekday_rate),
                'source' => 'seasonal_rate',
                'high_weekday' => $highWeekday,
            ];
        }

        $baseRate = UnitTypeRate::query()
            ->where('unit_type_id', $unitTypeId)
            ->where('is_active', true)
            ->first();

        return [
            'rate' => (float) ($baseRate ? ($highWeekday ? $baseRate->high_weekday_rate : $baseRate->low_weekday_rate) : 0),
            'source' => 'unit_type_rate',
            'high_weekday' => $highWeekday,
        ];
    }

    private function monthlyRate(Unit $unit, CarbonInterface $date): array
    {
        $unitCustomRate = UnitCustomRate::query()->where('unit_id', $unit->id)->first();

        if ($unitCustomRate && (float) $unitCustomRate->monthly_rate > 0) {
            return [
                'rate' => (float) $unitCustomRate->monthly_rate,
                'source' => 'unit_custom_rate',
            ];
        }

        $baseRate = UnitTypeRate::query()
            ->where('unit_type_id', $unit->rate_unit_type_id ?: $unit->unit_type_id)
            ->where('is_active', true)
            ->first();

        return [
            'rate' => (float) ($baseRate?->monthly_rate ?? 0),
            'source' => 'unit_type_rate',
        ];
    }

    private function calculateTaxes(float $subtotal, int $nights, CarbonInterface $checkIn): float
    {
        $taxes = TaxFeeCustomization::query()
            ->where('is_expenses', false)
            ->whereDate('start_date', '<=', $checkIn->toDateString())
            ->where(function ($query) use ($checkIn) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $checkIn->toDateString());
            })
            ->get();

        $total = 0;

        foreach ($taxes as $tax) {
            if ($tax->has_max_length && $tax->max_length && $nights > $tax->max_length) {
                continue;
            }

            $amount = (float) $tax->amount;

            $total += match ($tax->method) {
                'percentage' => ($subtotal * $amount) / 100,
                'fixed_amount_per_night' => $amount * $nights,
                default => $amount,
            };
        }

        return round($total, 2);
    }

    private function isHighWeekday(CarbonInterface $date): bool
    {
        static $highWeekdays = null;

        if ($highWeekdays === null) {
            $highWeekdays = HighWeekday::query()
                ->pluck('day_name')
                ->map(fn (string $value) => mb_strtolower($value))
                ->all();
        }

        return in_array(mb_strtolower($date->format('l')), $highWeekdays, true);
    }
}
