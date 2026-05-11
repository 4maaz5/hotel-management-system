<?php

namespace App\Http\Controllers\Units;

use App\Http\Controllers\Controller;
use App\Models\RatePlan;
use App\Models\Unit;
use App\Models\UnitCustomRate;
use App\Models\UnitTypeCustomization;
use App\Support\UserActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RatePlanController extends Controller
{
    public function index(Request $request)
    {
        $query = RatePlan::with('meals');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%'.$request->name.'%');
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        if ($request->filled('meal')) {
            $query->whereHas('meals', function ($q) use ($request) {
                $q->where('meal_name', $request->meal);
            });
        }

        $ratePlans = $query->latest()->paginate(10);

        $allMeals = ['breakfast', 'lunch', 'dinner'];

        return view('admin.rate_plan.index', compact('ratePlans', 'allMeals'));
    }

    public function create()
    {
        $unitTypes = UnitTypeCustomization::all();
        $assignedUnitIds = UnitCustomRate::pluck('unit_id')->toArray();
        $availableUnits = Unit::where('is_active', true)->whereNotIn('id', $assignedUnitIds)->get();
        $assignedRates = UnitCustomRate::with('unit', 'unitType')->get();

        return view('admin.rate_plan.create', compact('unitTypes', 'availableUnits', 'assignedUnitIds', 'assignedRates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
            'status' => 'nullable|boolean',

            'rates' => 'array',
            'rates.*.unit_type_id' => 'required|exists:unit_types,id',
            'rates.*.daily_rate' => 'nullable|numeric|min:0',
            'rates.*.monthly_rate' => 'nullable|numeric|min:0',

            'meals' => 'array',
            'meals.*.meal_type' => 'required|string',
            'meals.*.adult_price' => 'nullable|numeric|min:0',
            'meals.*.child_price' => 'nullable|numeric|min:0',
            'meals.*.enabled' => 'nullable|boolean',
        ]);
        $ratePlan = DB::transaction(function () use ($request) {
            $ratePlan = RatePlan::create([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->status ?? 1,
            ]);

            if ($request->has('rates')) {
                $ratesToAttach = [];
                foreach ($request->rates as $rate) {
                    if (empty($rate['daily_rate']) && empty($rate['monthly_rate'])) {
                        continue;
                    }

                    if (! isset($rate['daily_rate'], $rate['monthly_rate'])) {
                        continue;
                    }

                    $ratesToAttach[$rate['unit_type_id']] = [
                        'daily_rate' => $rate['daily_rate'],
                        'monthly_rate' => $rate['monthly_rate'],
                    ];
                }

                if (! empty($ratesToAttach)) {
                    $ratePlan->unitTypeRates()->sync($ratesToAttach);

                }
            }
            if ($request->has('meals')) {
                foreach ($request->meals as $meal) {
                    if (! empty($meal['enabled']) && $meal['enabled'] == 1) {
                        $ratePlan->meals()->create(['meal_name' => $meal['meal_type'], 'adult_price' => $meal['adult_price'] ?? null, 'child_price' => $meal['child_price'] ?? null]);
                    }
                }
            }

            return $ratePlan->fresh(['unitTypeRates', 'meals']);
        });

        app(UserActivityLogger::class)->log(
            'pricing',
            'created',
            $ratePlan,
            "Created rate plan {$ratePlan->name}",
            [],
            $this->ratePlanActivityData($ratePlan),
            ['area' => 'rate_plan']
        );

        return redirect()->route('setup-sidebar.rate_plan.index')
            ->with('success', __('messages.rate_plan_created_successfully'));
    }

    public function edit($id)
    {
        $ratePlan = RatePlan::with(['unitTypeRates', 'meals'])->findOrFail($id);

        $unitTypes = UnitTypeCustomization::all();

        $assignedUnitIds = UnitCustomRate::pluck('unit_id')->toArray();
        $availableUnits = Unit::where('is_active', true)
            ->whereNotIn('id', $assignedUnitIds)
            ->get();
        $assignedRates = UnitCustomRate::with('unit', 'unitType')->get();

        return view('admin.rate_plan.edit', compact(
            'ratePlan',
            'unitTypes',
            'availableUnits',
            'assignedRates'
        ));
    }

    public function view($id)
    {
        $ratePlan = RatePlan::with(['unitTypeRates', 'meals'])->findOrFail($id);

        $unitTypes = UnitTypeCustomization::all();

        $assignedUnitIds = UnitCustomRate::pluck('unit_id')->toArray();
        $availableUnits = Unit::where('is_active', true)
            ->whereNotIn('id', $assignedUnitIds)
            ->get();
        $assignedRates = UnitCustomRate::with('unit', 'unitType')->get();

        return view('admin.rate_plan.view', compact(
            'ratePlan',
            'unitTypes',
            'availableUnits',
            'assignedRates'
        ));
    }

    public function update(Request $request, $id)
    {
        $ratePlan = RatePlan::findOrFail($id);
        $before = $this->ratePlanActivityData($ratePlan->load(['unitTypeRates', 'meals']));

        $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
            'status' => 'nullable|boolean',

            'rates' => 'nullable|array',
            'rates.*.unit_type_id' => 'required|exists:unit_types,id',
            'rates.*.daily_rate' => 'nullable|numeric|min:0',
            'rates.*.monthly_rate' => 'nullable|numeric|min:0',

            'meals' => 'nullable|array',
            'meals.*.meal_type' => 'required|string',
            'meals.*.adult_price' => 'nullable|numeric|min:0',
            'meals.*.child_price' => 'nullable|numeric|min:0',
            'meals.*.enabled' => 'nullable|boolean',
        ]);
        $ratePlan = DB::transaction(function () use ($request, $ratePlan) {
            $ratePlan->update([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->status,
            ]);

            $ratesToSync = [];
            if ($request->has('rates')) {
                foreach ($request->rates as $rate) {
                    if (empty($rate['daily_rate']) && empty($rate['monthly_rate'])) {
                        continue;
                    }
                    $ratesToSync[$rate['unit_type_id']] = [
                        'daily_rate' => $rate['daily_rate'] ?? null,
                        'monthly_rate' => $rate['monthly_rate'] ?? null,
                    ];
                }
            }
            $ratePlan->unitTypeRates()->sync($ratesToSync);

            $ratePlan->meals()->delete();

            if ($request->has('meals')) {
                foreach ($request->meals as $meal) {
                    if (! empty($meal['enabled']) && $meal['enabled'] == 1) {
                        $ratePlan->meals()->create([
                            'meal_name' => $meal['meal_type'],
                            'adult_price' => $meal['adult_price'] ?? null,
                            'child_price' => $meal['child_price'] ?? null,
                        ]);
                    }
                }
            }

            return $ratePlan->fresh(['unitTypeRates', 'meals']);
        });

        app(UserActivityLogger::class)->log(
            'pricing',
            'updated',
            $ratePlan,
            "Updated rate plan {$ratePlan->name}",
            $before,
            $this->ratePlanActivityData($ratePlan),
            ['area' => 'rate_plan']
        );

        return redirect()->route('setup-sidebar.rate_plan.index')
            ->with('success', __('messages.rate_plan_updated_successfully'));
    }

    public function delete($id)
    {
        $ratePlan = RatePlan::findOrfail($id);
        $ratePlan->delete();

        return redirect()->route('setup-sidebar.rate_plan.index')->with('danger', __('messages.rate_plan_deleted_successfully'));
    }

    protected function ratePlanActivityData(RatePlan $ratePlan): array
    {
        $ratePlan->loadMissing(['unitTypeRates', 'meals']);

        return [
            'name' => $ratePlan->name,
            'description' => $ratePlan->description,
            'is_active' => (bool) $ratePlan->is_active,
            'rates' => $ratePlan->unitTypeRates->mapWithKeys(fn ($unitType) => [
                $unitType->id => [
                    'daily_rate' => (float) ($unitType->pivot->daily_rate ?? 0),
                    'monthly_rate' => (float) ($unitType->pivot->monthly_rate ?? 0),
                ],
            ])->all(),
            'meals' => $ratePlan->meals->map(fn ($meal) => [
                'meal_name' => $meal->meal_name,
                'adult_price' => (float) ($meal->adult_price ?? 0),
                'child_price' => (float) ($meal->child_price ?? 0),
            ])->all(),
        ];
    }
}
