<?php

namespace App\Http\Controllers\Vehicles;

use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class AccidentController extends Controller
{
    public function index()
    {
        $accidents = Accident::with(['vehicle', 'driver'])->get();
        $vehicles = Vehicle::all();
        $drivers = Driver::all();

        return view('Admin.Backend.Vehicles.accidents', compact('accidents', 'vehicles', 'drivers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'required|exists:drivers,id',
            'accident_date' => 'required|date',
            'fine_percentage' => 'nullable|numeric|min:0|max:100',
            'repair_cost' => 'nullable|numeric',
            'insurance_coverage' => 'required|in:yes,no,partial',
            'repair_status' => 'required|in:pending,in_progress,completed',
            'description' => 'nullable|string',
        ]);

        Accident::create($request->all()); // Now this will work

        return redirect()->back()->with('success', __('messages.accident_added_successfully'));
    }

    public function update(Request $request, Accident $accident)
    {
        // Validate inputs
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'required|exists:drivers,id',
            'accident_date' => 'required|date',
            'fine_percentage' => 'nullable|numeric|min:0|max:100',
            'repair_cost' => 'nullable|numeric',
            'insurance_coverage' => 'required|in:yes,no,partial',
            'repair_status' => 'required|in:pending,in_progress,completed',
            'description' => 'nullable|string',
        ]);

        try {
            // Update the accident
            $accident->update([
                'vehicle_id' => $request->vehicle_id,
                'driver_id' => $request->driver_id,
                'accident_date' => $request->accident_date,
                'fine_percentage' => $request->fine_percentage,
                'repair_cost' => $request->repair_cost,
                'insurance_coverage' => $request->insurance_coverage,
                'repair_status' => $request->repair_status,
                'description' => $request->description,
            ]);

            return redirect()->back()->with('success', __('messages.accident_updated_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Accident $accident)
    {
        $accident->delete();

        return redirect()->back()->with('delete', __('messages.accident_deleted_successfully'));
    }
}
