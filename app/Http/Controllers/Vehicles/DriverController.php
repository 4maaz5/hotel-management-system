<?php

namespace App\Http\Controllers\Vehicles;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\DriverDocument;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DriverController extends Controller
{
    use ScopesTenantAccess;

    public function index()
    {
        $user = auth()->user();
        $vehicles = $this->scopeVehiclesForUser(Vehicle::query(), $user)->get();
        $drivers = $this->scopeDriversForUser(Driver::with('vehicle'), $user)->get();

        return view('Admin.Backend.Vehicles.driver', compact('vehicles', 'drivers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => ['required', $this->vehicleExistsRuleForUser($request->user())],
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'id_number' => 'nullable|string|max:50',
            'driver_files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'driver_start_date.*' => 'nullable|date',
            'driver_end_date.*' => 'nullable|date|after_or_equal:driver_start_date.*',
        ]);

        DB::beginTransaction();

        try {
            $driver = Driver::create([
                'vehicle_id' => $request->vehicle_id,
                'name' => $request->name,
                'phone' => $request->phone,
                'id_number' => $request->id_number,
            ]);

            if ($request->hasFile('driver_files')) {
                foreach ($request->driver_files as $index => $file) {
                    if (! $file) {
                        continue;
                    }

                    $path = $file->store("drivers/{$driver->id}/documents", 'public');

                    DriverDocument::create([
                        'driver_id' => $driver->id,
                        'file_path' => $path,
                        'start_date' => $request->driver_start_date[$index] ?? null,
                        'end_date' => $request->driver_end_date[$index] ?? null,
                    ]);
                }
            }

            DB::commit();

            return redirect()->back()->with('success', __('messages.driver_added_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, Driver $driver)
    {
        $driver = $this->scopeDriversForUser(Driver::query(), $request->user())->findOrFail($driver->id);

        //  Validation
        $request->validate([
            'vehicle_id' => ['required', $this->vehicleExistsRuleForUser($request->user())],
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'id_number' => 'nullable|string|max:255',

            'driver_files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'driver_start_date.*' => 'nullable|date',
            'driver_end_date.*' => 'nullable|date|after_or_equal:driver_start_date.*',
        ]);

        DB::beginTransaction();

        try {
            // Update driver basic info
            $driver->update([
                'vehicle_id' => $request->vehicle_id,
                'name' => $request->name,
                'phone' => $request->phone,
                'id_number' => $request->id_number,
            ]);

            //  If new files uploaded, store them
            if ($request->hasFile('driver_files')) {

                // Optionally: delete old documents if you want to replace them
                // foreach ($driver->documents as $doc) {
                //     if (Storage::disk('public')->exists($doc->file_path)) {
                //         Storage::disk('public')->delete($doc->file_path);
                //     }
                //     $doc->delete();
                // }

                // Store new documents
                foreach ($request->driver_files as $index => $file) {
                    if (! $file) {
                        continue;
                    }

                    $path = $file->store("drivers/{$driver->id}/documents", 'public');

                    DriverDocument::create([
                        'driver_id' => $driver->id,
                        'file_path' => $path,
                        'start_date' => $request->driver_start_date[$index] ?? null,
                        'end_date' => $request->driver_end_date[$index] ?? null,
                    ]);
                }
            }

            DB::commit();

            return redirect()->back()->with('success', __('messages.driver_updated_successfully'));

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Driver $driver)
    {
        $driver = $this->scopeDriversForUser(Driver::with('documents'), auth()->user())->findOrFail($driver->id);

        DB::beginTransaction();

        try {
            // Delete all driver documents from storage & DB
            foreach ($driver->documents as $doc) {
                if (Storage::disk('public')->exists($doc->file_path)) {
                    Storage::disk('public')->delete($doc->file_path);
                }
                $doc->delete();
            }

            // Delete driver
            $driver->delete();

            DB::commit();

            return redirect()->back()->with('delete', __('messages.driver_deleted_successfully'));

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', $e->getMessage());
        }
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
}
