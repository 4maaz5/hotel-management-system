<?php

namespace App\Http\Controllers\Units;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\Unit;
use Illuminate\Http\Request;

class AmenityController extends Controller
{
    public function index(Request $request)
    {
        $query = Amenity::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%'.$request->name.'%');
        }

        if ($request->filled('description')) {
            $query->where('description', 'like', '%'.$request->description.'%');
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $amenities = $query->paginate(10)->withQueryString();

        return view('admin.amenities.index', compact('amenities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Amenity::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->back()
            ->with('success', __('messages.amenity_created_successfully'));
    }

    public function update(Request $request, Amenity $amenity)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $amenity->update($validated);

        return redirect()
            ->back()
            ->with('success', __('messages.amenity_updated_successfully'));
    }

    public function delete(Amenity $amenity)
    {
        $amenity->delete();

        return redirect()
            ->back()
            ->with('danger', __('messages.amenity_deleted_successfully'));
    }

    public function applyToAllUnits(Request $request)
    {
        $amenityId = $request->amenity_id;

        $units = Unit::where('is_active', 1)->get();

        if ($units->isEmpty()) {
            return back()->with(
                'danger',
                __('messages.no_active_units_found')
            );
        }

        foreach ($units as $unit) {

            if ($unit->amenities()->where('amenity_id', $amenityId)->exists()) {
                continue;
            }

            $unit->amenities()->attach($amenityId);
        }

        return back()->with('success', __('messages.amenity_applied_successfully'));
    }
}
