<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\FacilityCategory;
use App\Models\PropertyFacility;
use Illuminate\Http\Request;

class PropertyFacilityController extends Controller
{
    public function index(Request $request)
    {
        $categories = FacilityCategory::where('status', 1)->get();

        $facilities = Facility::where('status', 1)->get();

        $query = PropertyFacility::with([
            'category',
            'facility',
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('facility_name')) {
            $query->whereHas('facility', function ($q) use ($request) {
                $q->where('name_en', 'like', '%'.$request->facility_name.'%');
            });
        }

        if ($request->filled('category_id')) {
            $query->where('facility_category_id', $request->category_id);
        }

        $propertyFacilities = $query->latest()->paginate(10);

        return view('admin.property_facilities.index', compact(
            'categories',
            'facilities',
            'propertyFacilities'
        ));
    }

    public function getFacilities(Request $request)
    {
        return Facility::where('facility_category_id', $request->category_id)
            ->where('status', 1)
            ->select('id', 'name')
            ->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'facility_category_id' => 'required|exists:facility_categories,id',
            'facility_id' => 'required|exists:facilities,id',
            'description_en' => 'nullable|string|max:500',
        ]);

        PropertyFacility::create([
            'facility_category_id' => $request->facility_category_id,
            'facility_id' => $request->facility_id,
            'description' => $request->description_en,
            'status' => true,
        ]);

        return redirect()->back()
            ->with('success', __('messages.property_facility_added_successfully'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'facility_category_id' => 'required',
            'facility_id' => 'required',
        ]);

        PropertyFacility::findOrFail($id)->update([
            'facility_category_id' => $request->facility_category_id,
            'facility_id' => $request->facility_id,
            'description' => $request->description_en,
        ]);

        return back()->with('success', __('messages.property_facility_updated_successfully'));
    }

    public function toggleStatus($id)
    {
        $facility = PropertyFacility::findOrFail($id);

        $facility->update([
            'status' => ! $facility->status,
        ]);

        return back()->with('success', __('messages.status_updated_successfully'));
    }

    public function delete($id)
    {
        PropertyFacility::findOrFail($id)->delete();

        return back()->with('danger', __('messages.property_facility_deleted'));
    }
}
