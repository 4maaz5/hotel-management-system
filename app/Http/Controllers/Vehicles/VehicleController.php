<?php

namespace App\Http\Controllers\Vehicles;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Vehicle;
use App\Models\VehicleDocuments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class VehicleController extends Controller
{
    public function index()
    {
        $branches = Branch::all();
        $vehicles = Vehicle::with(['documents', 'branch'])
            ->latest()
            ->get();

        return view('Admin.Backend.Vehicles.index', compact('branches', 'vehicles'));
    }

    public function store(Request $request)
    {
        //  Validation
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'model' => 'nullable|string|max:255',
            'plate_number' => 'required|string|max:255|unique:vehicles,plate_number',
            'owner_name' => 'nullable|string|max:255',
            'owner_contact' => 'nullable|string|max:255',
            'owner_iqama' => 'nullable|string|max:255',

            'vehicle_files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'vehicle_start_date.*' => 'nullable|date',
            'vehicle_end_date.*' => 'nullable|date|after_or_equal:vehicle_start_date.*',
        ]);

        DB::beginTransaction();

        try {

            //  Create Vehicle
            $vehicle = Vehicle::create([
                'branch_id' => $request->branch_id,
                'name' => $request->name,
                'model' => $request->model,
                'plate_number' => $request->plate_number,
                'owner_name' => $request->owner_name,
                'owner_contact' => $request->owner_contact,
                'owner_iqama' => $request->owner_iqama,
            ]);

            //  Handle Vehicle Documents
            if ($request->hasFile('vehicle_files')) {

                foreach ($request->vehicle_files as $index => $file) {

                    if (! $file) {
                        continue;
                    }

                    // Store file
                    $path = $file->store(
                        "vehicles/{$vehicle->id}/documents",
                        'public'
                    );

                    VehicleDocuments::create([
                        'vehicle_id' => $vehicle->id,
                        'file_path' => $path,
                        'start_date' => $request->vehicle_start_date[$index] ?? null,
                        'end_date' => $request->vehicle_end_date[$index] ?? null,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->back()
                ->with('success', __('messages.vehicle_added_successfully'));

        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        // dd($vehicle);

        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'model' => 'nullable|string|max:255',
            'plate_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('vehicles', 'plate_number')->ignore($vehicle),
            ],
            'owner_name' => 'nullable|string|max:255',
            'owner_contact' => 'nullable|string|max:255',
            'owner_iqama' => 'nullable|string|max:255',

            'vehicle_files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'vehicle_start_date.*' => 'nullable|date',
            'vehicle_end_date.*' => 'nullable|date|after_or_equal:vehicle_start_date.*',
        ]);

        DB::beginTransaction();

        try {

            // Update vehicle info
            $vehicle->update([
                'branch_id' => $request->branch_id,
                'name' => $request->name,
                'model' => $request->model,
                'plate_number' => $request->plate_number,
                'owner_name' => $request->owner_name,
                'owner_contact' => $request->owner_contact,
                'owner_iqama' => $request->owner_iqama,
            ]);

            // Replace documents if new files uploaded
            if ($request->hasFile('vehicle_files')) {

                // Delete old documents
                // foreach ($vehicle->documents as $doc) {
                //     // if (Storage::disk('public')->exists($doc->file_path)) {
                //     //     Storage::disk('public')->delete($doc->file_path);
                //     // }
                //     // $doc->delete();
                // }

                // Store new documents
                foreach ($request->vehicle_files as $index => $file) {

                    if (! $file) {
                        continue;
                    }

                    $path = $file->store(
                        "vehicles/{$vehicle->id}/documents",
                        'public'
                    );

                    VehicleDocuments::create([
                        'vehicle_id' => $vehicle->id,
                        'file_path' => $path,
                        'start_date' => $request->vehicle_start_date[$index] ?? null,
                        'end_date' => $request->vehicle_end_date[$index] ?? null,
                    ]);
                }
            }

            DB::commit();

            return redirect()->back()
                ->with('success', __('messages.vehicle_updated_successfully'));

        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(Vehicle $vehicle)
    {
        DB::beginTransaction();

        try {
            //  Delete all associated documents (DB + storage)
            foreach ($vehicle->documents as $doc) {
                if (Storage::disk('public')->exists($doc->file_path)) {
                    Storage::disk('public')->delete($doc->file_path);
                }
                $doc->delete();
            }

            //  Delete the vehicle itself
            $vehicle->delete();

            DB::commit();

            return redirect()->back()->with('delete', __('messages.vehicle_deleted_successfully'));

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
