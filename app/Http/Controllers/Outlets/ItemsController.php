<?php

namespace App\Http\Controllers\Outlets;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\ItemCategory;
use App\Models\OutletItem;
use App\Models\OutletSetup;
use App\Support\PropertyContext;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ItemsController extends Controller
{
    use ScopesTenantAccess;

    public function index(Request $request)
    {
        $query = OutletItem::with(['outlet', 'category'])
            ->whereHas('outlet', fn ($outletQuery) => $this->scopeOutletsForRequest($outletQuery, $request));
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('outlet')) {
            $outlet = $this->scopeOutletsForRequest(OutletSetup::query(), $request)->findOrFail($request->outlet);
            $query->where('outlet_id', $outlet->id);
        }
        if ($request->filled('category')) {
            $category = $this->scopeCategoriesForRequest(ItemCategory::query(), $request)->findOrFail($request->category);
            $query->where('category_id', $category->id);
        }
        if ($request->filled('name')) {
            $query->where('name', 'like', '%'.$request->name.'%');
        }
        if ($request->filled('price')) {
            $query->where('price', $request->price);
        }

        $outletItems = $query->latest()->get();

        $outlets = $this->scopeOutletsForRequest(OutletSetup::query(), $request)->get();
        $categories = $this->scopeCategoriesForRequest(ItemCategory::query(), $request)->get();

        return view('admin.items.index', compact(
            'outletItems',
            'outlets',
            'categories'
        ));
    }

    public function create()
    {
        $request = request();
        $outlets = $this->scopeOutletsForRequest(OutletSetup::where('status', true), $request)->get();
        $categories = $this->scopeCategoriesForRequest(ItemCategory::where('status', true), $request)->get();

        return view('admin.items.create', compact('outlets', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'type' => 'required|string',
            'outlet_id' => [
                'required',
                Rule::exists('outlet_setups', 'id')->where(fn ($query) => $this->scopeOutletsForRequest($query, $request)),
            ],
            'category_id' => [
                'nullable',
                Rule::exists('item_categories', 'id')->where(fn ($query) => $this->scopeCategoryRowsForRequest($query, $request)),
            ],
            'description' => 'nullable|string|max:600',
            'price' => 'required|numeric|min:0',
        ]);

        OutletItem::create([
            'company_id' => $this->companyIdForRequest($request),
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
        $item = $this->scopeItemsForRequest(OutletItem::query(), request())->findOrFail($id);

        $outlets = $this->scopeOutletsForRequest(OutletSetup::query(), request())->get();

        $categories = $this->scopeCategoriesForRequest(ItemCategory::where('outlet_id', $item->outlet_id), request())
            ->get();

        return view('admin.items.edit', compact(
            'item',
            'outlets',
            'categories'
        ));
    }

    public function update(Request $request, $id)
    {
        $item = $this->scopeItemsForRequest(OutletItem::query(), $request)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'type' => 'required|string',
            'outlet_id' => [
                'required',
                Rule::exists('outlet_setups', 'id')->where(fn ($query) => $this->scopeOutletsForRequest($query, $request)),
            ],
            'category_id' => [
                'nullable',
                Rule::exists('item_categories', 'id')->where(fn ($query) => $this->scopeCategoryRowsForRequest($query, $request)),
            ],
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
        $item = $this->scopeItemsForRequest(OutletItem::query(), request())->findOrFail($id);
        $item->delete();

        return redirect()->back()->with('danger', __('messages.outlet_item_deleted_successfully'));
    }

    public function getCategories($outletId)
    {
        $outlet = $this->scopeOutletsForRequest(OutletSetup::query(), request())->findOrFail($outletId);
        $categories = $this->scopeCategoriesForRequest(ItemCategory::where('outlet_id', $outlet->id), request())
            ->select('id', 'name')
            ->get();

        return response()->json($categories);
    }

    private function scopeItemsForRequest($query, Request $request)
    {
        return $query->whereHas('outlet', fn ($outletQuery) => $this->scopeOutletsForRequest($outletQuery, $request));
    }

    private function scopeCategoriesForRequest($query, Request $request)
    {
        return $query->whereHas('outlet', fn ($outletQuery) => $this->scopeOutletsForRequest($outletQuery, $request));
    }

    private function scopeCategoryRowsForRequest($query, Request $request)
    {
        $outletIds = $this->scopeOutletsForRequest(OutletSetup::query(), $request)->pluck('id');

        return $query->whereIn('outlet_id', $outletIds);
    }

    private function scopeOutletsForRequest($query, Request $request)
    {
        $user = $request->user();

        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        $branchId = $this->currentBranchId($request);

        $query->where('company_id', $user->company_id);

        return $branchId ? $query->where('branch_id', $branchId) : $query;
    }

    private function currentBranchId(Request $request): ?int
    {
        $user = $request->user();

        if ($user?->branch_id) {
            return (int) $user->branch_id;
        }

        $sessionBranchId = $request->session()->get('branch_id');
        if ($sessionBranchId) {
            return (int) $sessionBranchId;
        }

        $property = app(PropertyContext::class)->property();

        return $property?->branch_id ? (int) $property->branch_id : null;
    }

    private function companyIdForRequest(Request $request): ?int
    {
        return $this->isSuperAdmin($request->user()) ? null : $request->user()->company_id;
    }
}
