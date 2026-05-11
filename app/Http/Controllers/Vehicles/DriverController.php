<?php

namespace App\Http\Controllers\Vehicles;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\DriverDocument;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DriverController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::all();
        $drivers = Driver::all();

        return view('Admin.Backend.Vehicles.driver', compact('vehicles', 'drivers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
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
        //  Validation
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
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
}
