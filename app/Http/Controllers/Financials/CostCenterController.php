<?php

namespace App\Http\Controllers\Financials;

use App\Http\Controllers\Controller;
use App\Models\CostCenter;
use App\Models\CostCenterCategory;
use Illuminate\Http\Request;

class CostCenterController extends Controller
{
    public function index(Request $request)
    {
        $categories = CostCenterCategory::all();

        $query = CostCenter::with('category');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%'.$request->name.'%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $costCenters = $query->latest()->get();

        return view('admin.cost_centers.index', compact('costCenters', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:cost_center_categories,id',
            'description' => 'nullable|string',
        ]);

        CostCenter::create($validated);

        return redirect()->back()->with('success', __('messages.cost_center_added_successfully'));
    }

    public function update(Request $request, CostCenter $costCenter)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:cost_center_categories,id',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $costCenter->update($validated);

        return redirect()->back()->with('success', __('messages.cost_center_updated_successfully'));
    }

    public function delete(CostCenter $costCenter)
    {
        $costCenter->delete();

        return redirect()->back()->with('danger', __('messages.cost_center_deleted_successfully'));
    }
}
