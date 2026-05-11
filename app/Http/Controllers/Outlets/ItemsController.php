<?php

namespace App\Http\Controllers\Outlets;

use App\Http\Controllers\Controller;
use App\Models\ItemCategory;
use App\Models\OutletItem;
use App\Models\OutletSetup;
use Illuminate\Http\Request;

class ItemsController extends Controller
{
    public function index(Request $request)
    {
        $query = OutletItem::with(['outlet', 'category']);
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('outlet')) {
            $query->where('outlet_id', $request->outlet);
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('name')) {
            $query->where('name', 'like', '%'.$request->name.'%');
        }
        if ($request->filled('price')) {
            $query->where('price', $request->price);
        }

        $outletItems = $query->latest()->get();

        $outlets = OutletSetup::all();
        $categories = ItemCategory::all();

        return view('admin.items.index', compact(
            'outletItems',
            'outlets',
            'categories'
        ));
    }

    public function create()
    {
        $outlets = OutletSetup::where('status', true)->get();
        $categories = ItemCategory::where('status', true)->get();

        return view('admin.items.create', compact('outlets', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'type' => 'required|string',
            'outlet_id' => 'required|exists:outlet_setups,id',
            'category_id' => 'nullable|exists:item_categories,id',
            'description' => 'nullable|string|max:600',
            'price' => 'required|numeric|min:0',
        ]);

        OutletItem::create([
            'name' => $request->name,
            'type' => $request->type,
            'outlet_id' => $request->outlet_id,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'price' => $request->price,

            'no_tax' => $request->no_tax === 'on',
            'no_price' => $request->no_price === 'on',
            'price_is_user_defined' => $request->price_is_user_defined === 'on',
        ]);

        if ($request->action === 'save_new') {
            return redirect()->back()
                ->with('success', __('messages.item_created_successfully'));
        }

        return redirect()
            ->route('setup-sidebar.items.index')
            ->with('success', __('messages.item_created_successfully'));
    }

    public function edit($id)
    {
        $item = OutletItem::findOrFail($id);

        $outlets = OutletSetup::all();

        $categories = ItemCategory::where('outlet_id', $item->outlet_id)
            ->get();

        return view('admin.items.edit', compact(
            'item',
            'outlets',
            'categories'
        ));
    }

    public function update(Request $request, $id)
    {
        $item = OutletItem::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'type' => 'required|string',
            'outlet_id' => 'required|exists:outlet_setups,id',
            'category_id' => 'nullable|exists:item_categories,id',
            'description' => 'nullable|string|max:600',
            'price' => 'required|numeric|min:0',
        ]);

        $item->update([
            'name' => $request->name,
            'type' => $request->type,
            'outlet_id' => $request->outlet_id,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'price' => $request->price,

            'status' => $request->has('status') ? 1 : 0,
            'no_tax' => $request->has('no_tax') ? 1 : 0,
            'no_price' => $request->has('no_price') ? 1 : 0,
            'price_is_user_defined' => $request->has('price_is_user_defined') ? 1 : 0,
        ]);

        return redirect()
            ->route('setup-sidebar.items.index')
            ->with('success', __('messages.item_updated_successfully'));
    }

    public function delete($id)
    {
        $item = OutletItem::findOrfail($id);
        $item->delete();

        return redirect()->back()->with('danger', __('messages.outlet_item_deleted_successfully'));
    }

    public function getCategories($outletId)
    {
        $categories = ItemCategory::where('outlet_id', $outletId)
            ->select('id', 'name')
            ->get();

        return response()->json($categories);
    }
}
