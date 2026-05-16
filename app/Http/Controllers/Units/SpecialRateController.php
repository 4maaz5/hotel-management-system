<?php

namespace App\Http\Controllers\Units;

use App\Http\Controllers\Controller;
use App\Models\SpecialRate;
use App\Models\SpecialUnitTypeRate;
use App\Models\Unit;
use App\Models\UnitCustomRate;
use App\Models\UnitTypeCustomization;
use App\Support\UserActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpecialRateController extends Controller
{
    public function index(Request $request)
    {
        $query = SpecialRate::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%'.$request->name.'%');
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        if ($request->filled('start_date_from')) {
            $query->whereDate('start_date', '>=', $request->start_date_from);
        }

        if ($request->filled('start_date_to')) {
            $query->whereDate('start_date', '<=', $request->start_date_to);
        }

        if ($request->filled('end_date_from')) {
            $query->whereDate('end_date', '>=', $request->end_date_from);
        }

        if ($request->filled('end_date_to')) {
            $query->whereDate('end_date', '<=', $request->end_date_to);
        }

        if ($request->filled('rate')) {
            $query->whereHas('unitRates', function ($q) use ($request) {
                $q->where('rate', $request->rate);
            });
        }

        if ($request->filled('min_rate')) {
            $query->whereHas('unitRates', function ($q) use ($request) {
                $q->where('min_rate', $request->min_rate);
            });
        }

        $specialRates = $query->latest()->paginate(10);

        return view('admin.special_rate.index', compact('specialRates'));
    }

    public function create()
    {
        $unitTypes = UnitTypeCustomization::all();
        $availableUnits = Unit::where('is_active', true)
            ->get();

        $assignedUnitIds = UnitCustomRate::pluck('unit_id')->toArray();
        $availableUnits = Unit::where('is_active', true)->whereNotIn('id', $assignedUnitIds)->get();
        $assignedRates = UnitCustomRate::with('unit', 'unitType')->get();

        return view('admin.special_rate.create', compact('unitTypes', 'availableUnits', 'assignedRates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $overlap = SpecialRate::where(function ($q) use ($request) {
            $q->whereBetween('start_date', [$request->start_date, $request->end_date])
                ->orWhereBetween('end_date', [$request->start_date, $request->end_date]);
        })->exists();

        if ($overlap) {
            return back()->with(['danger' => __('messages.season_date_range_overlaps_existing_season')]);
        }

        $specialRate = DB::transaction(function () use ($request) {

            $season = SpecialRate::create([
                'name' => $request->name,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);

            foreach ($request->rates as $rate) {

                SpecialUnitTypeRate::create([
                    'special_rate_id' => $season->id,
                    'unit_type_id' => $rate['unit_id'],
                    'rate' => $rate['rate'],
                    'min_rate' => $rate['min_rate'],
                ]);
            }
            return $season->fresh(['unitRates']);
        });

        app(UserActivityLogger::class)->log(
            'pricing',
            'created',
            $specialRate,
            "Created special rate {$specialRate->name}",
            [],
            $this->specialRateActivityData($specialRate),
            ['area' => 'special_rate']
        );

        return redirect()->route('setup-sidebar.special_rate.index')->with('success', __('messages.special_rate_added_successfully'));
    }

    public function edit($id)
    {
        $seasonalRate = SpecialRate::with('unitRates')->findOrFail($id);

        $unitTypes = UnitTypeCustomization::all();

        $assignedUnitIds = UnitCustomRate::pluck('unit_id')->toArray();

        $availableUnits = Unit::where('is_active', true)
            ->whereNotIn('id', $assignedUnitIds)
            ->get();

        $assignedRates = UnitCustomRate::with('unit', 'unitType')->get();

        return view('admin.special_rate.edit', compact(
            'seasonalRate',
            'unitTypes',
            'availableUnits',
            'assignedRates'
        ));
    }

    public function view($id)
    {
        $seasonalRate = SpecialRate::with('unitRates')->findOrFail($id);

        $unitTypes = UnitTypeCustomization::all();

        $assignedUnitIds = UnitCustomRate::pluck('unit_id')->toArray();

        $availableUnits = Unit::where('is_active', true)
            ->whereNotIn('id', $assignedUnitIds)
            ->get();

        $assignedRates = UnitCustomRate::with('unit', 'unitType')->get();

        return view('admin.special_rate.view', compact(
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
            'rates' => 'nullable|array',
            'rates.*.unit_type_id' => 'required|exists:unit_types,id',
            'rates.*.rate' => 'nullable|numeric|min:0',
            'rates.*.min_rate' => 'nullable|numeric|min:0',
        ]);

        $seasonalRate = SpecialRate::findOrFail($id);
        $before = $this->specialRateActivityData($seasonalRate->load('unitRates'));

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
                        'rate' => $rateData['rate'],
                        'min_rate' => $rateData['min_rate'],
                    ]
                );
            }
        }

        app(UserActivityLogger::class)->log(
            'pricing',
            'updated',
            $seasonalRate,
            "Updated special rate {$seasonalRate->name}",
            $before,
            $this->specialRateActivityData($seasonalRate->fresh(['unitRates'])),
            ['area' => 'special_rate']
        );

        return redirect()->route('setup-sidebar.special_rate.index')
            ->with('success', __('messages.special_rate_updated_successfully'));
    }

    public function delete($id)
    {
        $seasonalRate = SpecialRate::findOrFail($id);
        $seasonalRate->delete();

        return redirect()->route('setup-sidebar.special_rate.index')
            ->with('danger', __('messages.special_rate_deleted_successfully'));
    }

    protected function specialRateActivityData(SpecialRate $specialRate): array
    {
        $specialRate->loadMissing('unitRates');

        return [
            'name' => $specialRate->name,
            'description' => $specialRate->description,
            'start_date' => $specialRate->start_date?->format('Y-m-d'),
            'end_date' => $specialRate->end_date?->format('Y-m-d'),
            'is_active' => (bool) $specialRate->is_active,
            'rates' => $specialRate->unitRates->mapWithKeys(fn ($rate) => [
                $rate->unit_type_id => [
                    'rate' => (float) $rate->rate,
                    'min_rate' => (float) $rate->min_rate,
                ],
            ])->all(),
        ];
    }
}
