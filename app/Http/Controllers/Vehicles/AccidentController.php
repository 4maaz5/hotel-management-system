<?php

namespace App\Http\Controllers\Vehicles;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccidentController extends Controller
{
    use ScopesTenantAccess;

    public function index()
    {
        $user = auth()->user();
        $accidents = $this->scopeAccidentsForUser(Accident::with(['vehicle', 'driver']), $user)->get();
        $vehicles = $this->scopeVehiclesForUser(Vehicle::query(), $user)->get();
        $drivers = $this->scopeDriversForUser(Driver::query(), $user)->get();

        return view('Admin.Backend.Vehicles.accidents', compact('accidents', 'vehicles', 'drivers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => ['required', $this->vehicleExistsRuleForUser($request->user())],
            'driver_id' => ['required', $this->driverForVehicleRule($request)],
            'accident_date' => 'required|date',
            'fine_percentage' => 'nullable|numeric|min:0|max:100',
            'repair_cost' => 'nullable|numeric',
            'insurance_coverage' => 'required|in:yes,no,partial',
            'repair_status' => 'required|in:pending,in_progress,completed',
            'description' => 'nullable|string',
        ]);

        Accident::create($validated);

        return redirect()->back()->with('success', __('messages.accident_added_successfully'));
    }

    public function update(Request $request, Accident $accident)
    {
        $accident = $this->scopeAccidentsForUser(Accident::query(), $request->user())->findOrFail($accident->id);

        // Validate inputs
        $validated = $request->validate([
            'vehicle_id' => ['required', $this->vehicleExistsRuleForUser($request->user())],
            'driver_id' => ['required', $this->driverForVehicleRule($request)],
            'accident_date' => 'required|date',
            'fine_percentage' => 'nullable|numeric|min:0|max:100',
            'repair_cost' => 'nullable|numeric',
            'insurance_coverage' => 'required|in:yes,no,partial',
            'repair_status' => 'required|in:pending,in_progress,completed',
            'description' => 'nullable|string',
        ]);

        try {
            // Update the accident
            $accident->update($validated);

            return redirect()->back()->with('success', __('messages.accident_updated_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Accident $accident)
    {
        $accident = $this->scopeAccidentsForUser(Accident::query(), auth()->user())->findOrFail($accident->id);
        $accident->delete();

        return redirect()->back()->with('delete', __('messages.accident_deleted_successfully'));
    }

    private function scopeVehiclesForUser($query, $user)
    {
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        if ($user->branch_id) {
            return $query->where('branch_id', $user->branch_id);
        }

        return $query->where('company_id', $user->company_id);
    }

    private function scopeDriversForUser($query, $user)
    {
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        return $query->whereHas('vehicle', function ($vehicleQuery) use ($user) {
            if ($user->branch_id) {
                $vehicleQuery->where('branch_id', $user->branch_id);

                return;
            }

            $vehicleQuery->where('company_id', $user->company_id);
        });
    }

    private function scopeAccidentsForUser($query, $user)
    {
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        return $query->whereHas('vehicle', function ($vehicleQuery) use ($user) {
            if ($user->branch_id) {
                $vehicleQuery->where('branch_id', $user->branch_id);

                return;
            }

            $vehicleQuery->where('company_id', $user->company_id);
        });
    }

    private function vehicleExistsRuleForUser($user)
    {
        return Rule::exists('vehicles', 'id')->where(function ($query) use ($user) {
            if ($this->isSuperAdmin($user)) {
                return;
            }

            if ($user->branch_id) {
                $query->where('branch_id', $user->branch_id);

                return;
            }

            $query->where('company_id', $user->company_id);
        });
    }

    private function driverForVehicleRule(Request $request): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) use ($request): void {
            $exists = $this->scopeDriversForUser(Driver::query(), $request->user())
                ->whereKey($value)
                ->where('vehicle_id', $request->input('vehicle_id'))
                ->exists();

            if (! $exists) {
                $fail(__('validation.exists', ['attribute' => str_replace('_', ' ', $attribute)]));
            }
        };
    }
}
