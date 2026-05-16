<?php

namespace App\Http\Controllers\Outlets;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\ItemCategory;
use App\Models\OutletSetup;
use App\Support\PropertyContext;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ItemCategoryController extends Controller
{
    use ScopesTenantAccess;

    public function index(Request $request)
    {
        $query = ItemCategory::with('outlet')
            ->whereHas('outlet', fn ($outletQuery) => $this->scopeOutletsForRequest($outletQuery, $request));

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('outlet')) {
            $outlet = $this->scopeOutletsForRequest(OutletSetup::query(), $request)->findOrFail($request->outlet);
            $query->where('outlet_id', $outlet->id);
        }
        if ($request->filled('name')) {
            $query->where('name', 'like', '%'.$request->name.'%');
        }
        if ($request->filled('ntmp')) {
            $query->where('ntmp_category', $request->ntmp);
        }

        $categories = $query->latest()->get();

        $outlets = $this->scopeOutletsForRequest(OutletSetup::query(), $request)->get();

        return view('admin.item_category.index',
            compact('categories', 'outlets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'outlet_id' => [
                'required',
                Rule::exists('outlet_setups', 'id')->where(fn ($query) => $this->scopeOutletsForRequest($query, $request)),
            ],
            'name' => 'required|string|max:200',
            'ntmp_category' => 'required|string',
            'description' => 'nullable|string',
        ]);

        ItemCategory::create([
            'company_id' => $this->companyIdForRequest($request),
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
        $category = $this->scopeCategoriesForRequest(ItemCategory::query(), $request)->findOrFail($id);

        $request->validate([
            'outlet_id' => [
                'required',
                Rule::exists('outlet_setups', 'id')->where(fn ($query) => $this->scopeOutletsForRequest($query, $request)),
            ],
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
        $category = $this->scopeCategoriesForRequest(ItemCategory::query(), request())->findOrFail($id);
        $category->delete();

        return redirect()->back()->with('danger', __('messages.category_deleted_successfully'));
    }

    private function scopeCategoriesForRequest($query, Request $request)
    {
        return $query->whereHas('outlet', fn ($outletQuery) => $this->scopeOutletsForRequest($outletQuery, $request));
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
