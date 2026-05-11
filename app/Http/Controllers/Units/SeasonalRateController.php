<?php

namespace App\Http\Controllers\Units;

use App\Http\Controllers\Controller;
use App\Models\SeasonalRate;
use App\Models\SeasonalUnitTypeRate;
use App\Models\Unit;
use App\Models\UnitCustomRate;
use App\Models\UnitTypeCustomization;
use App\Support\UserActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SeasonalRateController extends Controller
{
    public function index(Request $request)
    {
        $query = SeasonalRate::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%'.$request->name.'%');
        }

        if ($request->filled('start_from')) {
            $query->whereDate('start_date', '>=', $request->start_from);
        }

        if ($request->filled('start_to')) {
            $query->whereDate('start_date', '<=', $request->start_to);
        }

        if ($request->filled('end_from')) {
            $query->whereDate('end_date', '>=', $request->end_from);
        }

        if ($request->filled('end_to')) {
            $query->whereDate('end_date', '<=', $request->end_to);
        }

        if ($request->filled('status')) {

            $today = now()->toDateString();

            if ($request->status == 'active') {
                $query->where('is_active', 1)
                    ->whereDate('end_date', '>=', $today);
            }

            if ($request->status == 'inactive') {
                $query->where('is_active', 0);
            }

            if ($request->status == 'expired') {
                $query->whereDate('end_date', '<', $today);
            }
        }

        $seasons = $query->latest()->paginate(10);

        return view('admin.seasonal_rate.index', compact('seasons'));
    }

    public function create()
    {
        $unitTypes = UnitTypeCustomization::all();
        $availableUnits = Unit::where('is_active', true)
            ->get();

        $assignedUnitIds = UnitCustomRate::pluck('unit_id')->toArray();
        $availableUnits = Unit::where('is_active', true)->whereNotIn('id', $assignedUnitIds)->get();
        $assignedRates = UnitCustomRate::with('unit', 'unitType')->get();

        return view('admin.seasonal_rate.create', compact('unitTypes', 'availableUnits', 'assignedRates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $overlap = SeasonalRate::where(function ($q) use ($request) {
            $q->whereBetween('start_date', [$request->start_date, $request->end_date])
                ->orWhereBetween('end_date', [$request->start_date, $request->end_date]);
        })->exists();

        if ($overlap) {
            return back()->withErrors(['date' => 'Season date range overlaps existing season']);
        }

        $season = DB::transaction(function () use ($request) {

            $season = SeasonalRate::create([
                'name' => $request->name,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);
            // dd($request->rates);

            foreach ($request->rates as $rate) {

                SeasonalUnitTypeRate::create([
                    'seasonal_rate_id' => $season->id,
                    'unit_type_id' => $rate['unit_id'],
                    'low_weekday_rate' => $rate['low_rate'],
                    'high_weekday_rate' => $rate['high_rate'],
                    'daily_min_rate' => $rate['min_rate'],
                ]);
            }
            return $season->fresh(['unitRates']);
        });

        app(UserActivityLogger::class)->log(
            'pricing',
            'created',
            $season,
            "Created seasonal rate {$season->name}",
            [],
            $this->seasonalRateActivityData($season),
            ['area' => 'seasonal_rate']
        );

        return redirect()->route('setup-sidebar.seasonal_rate.index')->with('success', __('messages.season_created_successfully'));
    }

    public function edit($id)
    {
        $seasonalRate = SeasonalRate::with('unitRates')->findOrFail($id);

        $unitTypes = UnitTypeCustomization::all();

        $assignedUnitIds = UnitCustomRate::pluck('unit_id')->toArray();

        $availableUnits = Unit::where('is_active', true)
            ->whereNotIn('id', $assignedUnitIds)
            ->get();

        $assignedRates = UnitCustomRate::with('unit', 'unitType')->get();

        return view('admin.seasonal_rate.edit', compact(
            'seasonalRate',
            'unitTypes',
            'availableUnits',
            'assignedRates'
        ));
    }

    public function view($id)
    {
        $seasonalRate = SeasonalRate::with('unitRates')->findOrFail($id);

        $unitTypes = UnitTypeCustomization::all();

        $assignedUnitIds = UnitCustomRate::pluck('unit_id')->toArray();

        $availableUnits = Unit::where('is_active', true)
            ->whereNotIn('id', $assignedUnitIds)
            ->get();

        $assignedRates = UnitCustomRate::with('unit', 'unitType')->get();

        return view('admin.seasonal_rate.view', compact(
            'seasonalRate',
            'unitTypes',
            'availableUnits',
            'assignedRates'
        ));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'nullable|string',
            'rates' => 'array',
            'rates.*.unit_type_id' => 'required|exists:unit_types,id',
            'rates.*.low_rate' => 'nullable|numeric|min:0',
            'rates.*.high_rate' => 'nullable|numeric|min:0',
            'rates.*.min_rate' => 'nullable|numeric|min:0',
        ]);

        $seasonalRate = SeasonalRate::findOrFail($id);
        $before = $this->seasonalRateActivityData($seasonalRate->load('unitRates'));

        $seasonalRate->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => $validated['status'],
        ]);

        if (isset($validated['rates'])) {
            foreach ($validated['rates'] as $rateData) {
                $seasonalRate->unitRates()->updateOrCreate(
                    ['unit_type_id' => $rateData['unit_type_id']],
                    [
                        'low_weekday_rate' => $rateData['low_rate'],
                        'high_weekday_rate' => $rateData['high_rate'],
                        'daily_min_rate' => $rateData['min_rate'],
                    ]
                );
            }
        }

        app(UserActivityLogger::class)->log(
            'pricing',
            'updated',
            $seasonalRate,
            "Updated seasonal rate {$seasonalRate->name}",
            $before,
            $this->seasonalRateActivityData($seasonalRate->fresh(['unitRates'])),
            ['area' => 'seasonal_rate']
        );

        return redirect()->route('setup-sidebar.seasonal_rate.index')
            ->with('success', __('messages.seasonal_rate_updated_successfully'));
    }

    public function delete($id)
    {
        $seasonalRate = SeasonalRate::findOrFail($id);
        $seasonalRate->delete();

        return redirect()->route('setup-sidebar.seasonal_rate.index')
            ->with('danger', __('messages.seasonal_rate_deleted_successfully'));
    }

    public function seasonalCustomRate(Request $request)
    {
        $request->validate([
            'unit_type_id' => 'required|exists:unit_types,id',
            'unit_id' => 'required|exists:units,id',
            'low_weekday_rate' => 'nullable|numeric|min:0',
            'high_weekday_rate' => 'nullable|numeric|min:0',
            'daily_min_rate' => 'nullable|numeric|min:0',
        ]);

        UnitCustomRate::create([
            'unit_id' => $request->unit_id,
            'unit_type_id' => $request->unit_type_id,
            'low_weekday_rate' => $request->low_weekday_rate,
            'high_weekday_rate' => $request->high_weekday_rate,
            'daily_min_rate' => $request->daily_min_rate,
        ]);

        return back()->with('success', __('messages.unit_custom_rate_saved_successfully'));
    }

    public function deleteCustomRate($id)
    {
        $seasonal = UnitCustomRate::findOrfail($id);
        $seasonal->delete();

        return redirect()->back()->with('danger', __('messages.unit_custom_rate_deleted_successfully'));
    }

    protected function seasonalRateActivityData(SeasonalRate $seasonalRate): array
    {
        $seasonalRate->loadMissing('unitRates');

        return [
            'name' => $seasonalRate->name,
            'description' => $seasonalRate->description,
            'start_date' => $seasonalRate->start_date?->format('Y-m-d'),
            'end_date' => $seasonalRate->end_date?->format('Y-m-d'),
            'is_active' => (bool) $seasonalRate->is_active,
            'rates' => $seasonalRate->unitRates->mapWithKeys(fn ($rate) => [
                $rate->unit_type_id => [
                    'low_weekday_rate' => (float) $rate->low_weekday_rate,
                    'high_weekday_rate' => (float) $rate->high_weekday_rate,
                    'daily_min_rate' => (float) $rate->daily_min_rate,
                ],
            ])->all(),
        ];
    }
}
