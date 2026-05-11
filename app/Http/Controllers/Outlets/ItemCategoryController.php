<?php

namespace App\Http\Controllers\Outlets;

use App\Http\Controllers\Controller;
use App\Models\ItemCategory;
use App\Models\OutletSetup;
use Illuminate\Http\Request;

class ItemCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ItemCategory::with('outlet');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('outlet')) {
            $query->where('outlet_id', $request->outlet);
        }
        if ($request->filled('name')) {
            $query->where('name', 'like', '%'.$request->name.'%');
        }
        if ($request->filled('ntmp')) {
            $query->where('ntmp_category', $request->ntmp);
        }

        $categories = $query->latest()->get();

        $outlets = OutletSetup::all();

        return view('admin.item_category.index',
            compact('categories', 'outlets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'outlet_id' => 'required|exists:outlet_setups,id',
            'name' => 'required|string|max:200',
            'ntmp_category' => 'required|string',
            'description' => 'nullable|string',
        ]);

        ItemCategory::create([
            'status' => $request->has('status') ? 1 : 0,
            'outlet_id' => $request->outlet_id,
            'name' => $request->name,
            'ntmp_category' => $request->ntmp_category,
            'description' => $request->description,
        ]);

        return redirect()->back()
            ->with('success', __('messages.category_created_successfully'));
    }

    public function update(Request $request, $id)
    {
        $category = ItemCategory::findOrFail($id);

        $request->validate([
            'outlet_id' => 'required|exists:outlet_setups,id',
            'name' => 'required|string|max:200',
            'ntmp_category' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $category->update([
            'status' => $request->has('status'),
            'outlet_id' => $request->outlet_id,
            'name' => $request->name,
            'ntmp_category' => $request->ntmp_category,
            'description' => $request->description,
        ]);

        return redirect()->back()
            ->with('success', __('messages.category_updated_successfully'));
    }

    public function delete($id)
    {
        $category = ItemCategory::findOrfail($id);
        $category->delete();

        return redirect()->back()->with('danger', __('messages.category_deleted_successfully'));
    }
}
