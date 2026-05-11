<?php

namespace App\Http\Controllers\Units;

use App\Http\Controllers\Controller;
use App\Models\HighWeekday;
use App\Models\Unit;
use App\Models\UnitCustomRate;
use App\Models\UnitTypeCustomization;
use App\Models\UnitTypeRate;
use App\Support\UserActivityLogger;
use Illuminate\Http\Request;

class BaseRateController extends Controller
{
    public function index(Request $request)
    {
        $unitTypesQuery = UnitTypeCustomization::with(['unitType', 'rate']);

        if ($request->filled('unit_type_name')) {
            $unitTypesQuery->where('name', 'like', '%'.$request->unit_type_name.'%');
        }

        if ($request->filled('is_active')) {
            $unitTypesQuery->whereHas('unitType', function ($q) use ($request) {
                $q->where('is_active', $request->is_active);
            });
        } else {
            $unitTypesQuery->whereHas('unitType', function ($q) {
                $q->where('is_active', true);
            });
        }

        $unitTypes = $unitTypesQuery
            ->orderBy('id')
            ->get()
            ->unique('unit_type_id')
            ->values();

        $assignedUnitIds = UnitCustomRate::pluck('unit_id')->toArray();
        $availableUnits = Unit::where('is_active', true)
            ->whereNotIn('id', $assignedUnitIds)
            ->get();
        $assignedRates = UnitCustomRate::with('unit', 'unitType')->get();

        return view('admin.base_rate.index', compact(
            'unitTypes',
            'availableUnits',
            'assignedRates'
        ));
    }

    public function store(Request $request)
    {
        $unitTypeIds = array_keys($request->input('rates', []));
        $before = $this->baseRatesSnapshot($unitTypeIds);

        foreach ($request->rates as $unitTypeId => $rateData) {

            UnitTypeRate::updateOrCreate(
                ['unit_type_id' => $unitTypeId],
                [
                    'low_weekday_rate' => $rateData['low_weekday_rate'] ?? 0,
                    'high_weekday_rate' => $rateData['high_weekday_rate'] ?? 0,
                    'daily_min_rate' => $rateData['daily_min_rate'] ?? 0,
                    'monthly_rate' => $rateData['monthly_rate'] ?? 0,
                    'monthly_min_rate' => $rateData['monthly_min_rate'] ?? 0,
                    'is_active' => true,
                ]
            );
        }

        app(UserActivityLogger::class)->log(
            'pricing',
            'updated',
            null,
            'Updated base rates',
            $before,
            $this->baseRatesSnapshot($unitTypeIds),
            ['area' => 'base_rates']
        );

        return back()->with('success', __('messages.rate_saved_successfully'));
    }

    public function saveHighWeekdays(Request $request)
    {
        $before = HighWeekday::query()->pluck('day_name')->all();
        HighWeekday::truncate();

        if ($request->days) {
            foreach ($request->days as $day) {
                HighWeekday::create([
                    'day_name' => $day,
                ]);
            }
        }

        app(UserActivityLogger::class)->log(
            'pricing',
            'updated',
            null,
            'Updated high weekdays configuration',
            ['days' => $before],
            ['days' => $request->days ?? []],
            ['area' => 'high_weekdays']
        );

        return back()->with('success', __('messages.high_weekdays_updated_successfully'));
    }

    public function storeCustomRate(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'unit_type_id' => 'required|exists:unit_types,id',
        ]);

        $existingRate = UnitCustomRate::where('unit_id', $request->unit_id)->first();
        $before = $existingRate ? $this->customRateActivityData($existingRate) : [];

        $customRate = UnitCustomRate::updateOrCreate(
            ['unit_id' => $request->unit_id],
            [
                'unit_type_id' => $request->unit_type_id,
                'low_weekday_rate' => $request->low_weekday_rate,
                'high_weekday_rate' => $request->high_weekday_rate,
                'daily_min_rate' => $request->daily_min_rate,
                'monthly_rate' => $request->monthly_rate,
                'monthly_min_rate' => $request->monthly_min_rate,
            ]
        );

        app(UserActivityLogger::class)->log(
            'pricing',
            $before === [] ? 'created' : 'updated',
            $customRate,
            $before === []
                ? "Created custom rate for unit {$customRate->unit_id}"
                : "Updated custom rate for unit {$customRate->unit_id}",
            $before,
            $this->customRateActivityData($customRate),
            ['area' => 'custom_rate']
        );

        return back()->with('success', __('messages.custom_rate_saved_successfully'));
    }

    public function updateCustomRate(Request $request, $id)
    {
        $request->validate([
            'low_weekday_rate' => 'nullable|numeric',
            'high_weekday_rate' => 'nullable|numeric',
            'daily_min_rate' => 'nullable|numeric',
            'monthly_rate' => 'nullable|numeric',
            'monthly_min_rate' => 'nullable|numeric',
        ]);

        $customRate = UnitCustomRate::findOrFail($id);
        $before = $this->customRateActivityData($customRate);

        $customRate->update([
            'low_weekday_rate' => $request->low_weekday_rate,
            'high_weekday_rate' => $request->high_weekday_rate,
            'daily_min_rate' => $request->daily_min_rate,
            'monthly_rate' => $request->monthly_rate,
            'monthly_min_rate' => $request->monthly_min_rate,
        ]);

        app(UserActivityLogger::class)->log(
            'pricing',
            'updated',
            $customRate,
            "Updated custom rate for unit {$customRate->unit_id}",
            $before,
            $this->customRateActivityData($customRate->fresh()),
            ['area' => 'custom_rate']
        );

        return back()->with('success', __('messages.custom_rate_updated_successfully'));
    }

    public function destroy($id)
    {
        $rate = UnitCustomRate::findOrFail($id);

        $rate->delete();

        return redirect()->back()
            ->with('danger', __('messages.custom_rate_deleted_successfully'));
    }

    protected function baseRatesSnapshot(array $unitTypeIds): array
    {
        if ($unitTypeIds === []) {
            return [];
        }

        return UnitTypeRate::query()
            ->whereIn('unit_type_id', $unitTypeIds)
            ->get()
            ->mapWithKeys(fn (UnitTypeRate $rate) => [
                $rate->unit_type_id => [
                    'low_weekday_rate' => (float) $rate->low_weekday_rate,
                    'high_weekday_rate' => (float) $rate->high_weekday_rate,
                    'daily_min_rate' => (float) $rate->daily_min_rate,
                    'monthly_rate' => (float) $rate->monthly_rate,
                    'monthly_min_rate' => (float) $rate->monthly_min_rate,
                ],
            ])
            ->all();
    }

    protected function customRateActivityData(UnitCustomRate $customRate): array
    {
        return [
            'unit_id' => $customRate->unit_id,
            'unit_type_id' => $customRate->unit_type_id,
            'low_weekday_rate' => (float) $customRate->low_weekday_rate,
            'high_weekday_rate' => (float) $customRate->high_weekday_rate,
            'daily_min_rate' => (float) $customRate->daily_min_rate,
            'monthly_rate' => (float) $customRate->monthly_rate,
            'monthly_min_rate' => (float) $customRate->monthly_min_rate,
        ];
    }
}
