<?php

namespace App\Http\Controllers\Units;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\HighWeekday;
use App\Models\Unit;
use App\Models\UnitCustomRate;
use App\Models\UnitTypeCustomization;
use App\Models\UnitTypeRate;
use App\Support\PropertyContext;
use App\Support\TenantContext;
use App\Support\UserActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class BaseRateController extends Controller
{
    use ScopesTenantAccess;

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
        $availableUnits = Unit::with('unitTypeCustomization')
            ->where('is_active', true)
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
        $request->validate([
            'rates' => ['required', 'array'],
            'rates.*.low_weekday_rate' => ['nullable', 'numeric', 'min:0'],
            'rates.*.high_weekday_rate' => ['nullable', 'numeric', 'min:0'],
            'rates.*.daily_min_rate' => ['nullable', 'numeric', 'min:0'],
            'rates.*.monthly_rate' => ['nullable', 'numeric', 'min:0'],
            'rates.*.monthly_min_rate' => ['nullable', 'numeric', 'min:0'],
        ]);

        $companyId = $this->currentCompanyId($request);
        abort_unless($companyId, 422, 'Tenant context is required to save base rates.');

        $rates = $request->input('rates', []);
        $unitTypeIds = collect(array_keys($rates))
            ->map(fn ($unitTypeId) => (int) $unitTypeId)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $allowedUnitTypeIds = UnitTypeCustomization::query()
            ->where('company_id', $companyId)
            ->whereIn('unit_type_id', $unitTypeIds)
            ->pluck('unit_type_id')
            ->map(fn ($unitTypeId) => (int) $unitTypeId)
            ->all();

        if (count($allowedUnitTypeIds) !== count($unitTypeIds)) {
            throw ValidationException::withMessages([
                'rates' => __('validation.exists', ['attribute' => __('dashboard.unit_type')]),
            ]);
        }

        $before = $this->baseRatesSnapshot($unitTypeIds, $companyId);

        foreach ($rates as $unitTypeId => $rateData) {

            UnitTypeRate::updateOrCreate(
                [
                    'company_id' => $companyId,
                    'unit_type_id' => (int) $unitTypeId,
                ],
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
            $this->baseRatesSnapshot($unitTypeIds, $companyId),
            ['area' => 'base_rates']
        );

        return back()->with('success', __('messages.rate_saved_successfully'));
    }

    public function saveHighWeekdays(Request $request)
    {
        $companyId = $this->currentCompanyId($request);
        abort_unless($companyId, 422, 'Tenant context is required to save high weekdays.');

        $before = HighWeekday::query()
            ->where('company_id', $companyId)
            ->pluck('day_name')
            ->all();

        HighWeekday::query()
            ->where('company_id', $companyId)
            ->delete();

        if ($request->days) {
            foreach ($request->days as $day) {
                HighWeekday::create([
                    'company_id' => $companyId,
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
            'unit_id' => [
                'required',
                Rule::exists('units', 'id')->where(fn ($query) => $this->scopeUnitsForRequest($query, $request)),
            ],
            'unit_type_id' => [
                'required',
                Rule::exists('unit_type_customizations', 'unit_type_id')
                    ->where(fn ($query) => $query->where('company_id', $this->currentCompanyId($request))),
            ],
        ]);

        $unit = $this->scopeUnitsForRequest(Unit::with('unitTypeCustomization'), $request)
            ->findOrFail($request->integer('unit_id'));

        if ((int) $unit->unitTypeCustomization?->unit_type_id !== (int) $request->unit_type_id) {
            throw ValidationException::withMessages([
                'unit_id' => __('validation.exists', ['attribute' => __('dashboard.unit')]),
            ]);
        }

        $existingRate = UnitCustomRate::where('unit_id', $request->unit_id)->first();
        $before = $existingRate ? $this->customRateActivityData($existingRate) : [];
        $companyId = $this->currentCompanyId($request);

        $customRate = UnitCustomRate::updateOrCreate(
            [
                'company_id' => $companyId,
                'unit_id' => $request->unit_id,
            ],
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

    protected function baseRatesSnapshot(array $unitTypeIds, ?int $companyId = null): array
    {
        if ($unitTypeIds === []) {
            return [];
        }

        $query = UnitTypeRate::query()
            ->whereIn('unit_type_id', $unitTypeIds)
            ->when($companyId, fn ($query) => $query->where('company_id', $companyId));

        return $query->get()
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

    private function scopeUnitsForRequest($query, Request $request)
    {
        $user = $request->user();

        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        $branchId = $this->currentBranchId($request);

        $query->where('company_id', $user->company_id);

        return $branchId ? $query->where('branch_id', $branchId) : $query;
    }

    private function currentBranchId(Request $request): ?int
    {
        $user = $request->user();

        if ($user?->branch_id) {
            return (int) $user->branch_id;
        }

        $sessionBranchId = $request->session()->get('branch_id');
        if ($sessionBranchId) {
            return (int) $sessionBranchId;
        }

        $property = app(PropertyContext::class)->property();

        return $property?->branch_id ? (int) $property->branch_id : null;
    }

    private function currentCompanyId(Request $request): ?int
    {
        return app(TenantContext::class)->id() ?: $request->user()?->company_id;
    }
}
