<?php

namespace App\Http\Controllers\Outlets;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\OutletSetup;
use App\Support\PropertyContext;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OutletSetupController extends Controller
{
    use ScopesTenantAccess;

    public function index(Request $request)
    {
        $query = $this->scopeOutletsForRequest(OutletSetup::query(), $request);
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('name')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->name.'%');
            });
        }
        if ($request->filled('outlet_code')) {
            $query->where('outlet_code', $request->outlet_code);
        }
        if ($request->filled('operating_status')) {
            $query->where('operating_status', $request->operating_status);
        }
        $outlets = $query->latest()->paginate(10);

        return view('admin.outlet_setup.index', compact('outlets'));
    }

    public function store(Request $request)
    {
        $branchId = $this->currentBranchId($request);

        $validated = $request->validate([
            'operating_status' => 'required|string|max:50',
            'outlet_code' => [
                'required',
                'digits:3',
                Rule::unique('outlet_setups', 'outlet_code')
                    ->where('company_id', $this->companyIdForRequest($request))
                    ->where('branch_id', $branchId),
            ],
            'name' => 'required|string|max:200',
            'description' => 'nullable|string|max:600',
        ]);

        OutletSetup::create([
            'company_id' => $this->companyIdForRequest($request),
            'branch_id' => $branchId,
            'operating_status' => $validated['operating_status'],
            'outlet_code' => $validated['outlet_code'],
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        return redirect()->back()
            ->with('success', __('messages.outlet_created_successfully'));
    }

    public function update(Request $request, $id)
    {
        $outlet = $this->scopeOutletsForRequest(OutletSetup::query(), $request)->findOrFail($id);

        $validated = $request->validate([
            'status' => 'nullable|boolean',
            'operating_status' => 'required|string|max:50',
            'outlet_code' => [
                'required',
                'digits:3',
                Rule::unique('outlet_setups', 'outlet_code')
                    ->where('company_id', $outlet->company_id)
                    ->where('branch_id', $outlet->branch_id)
                    ->ignore($outlet->id),
            ],
            'name' => 'required|string|max:200',
            'description' => 'nullable|string|max:600',
        ]);

        $validated['status'] = $request->has('status') ? 1 : 0;

        $outlet->update($validated);

        return redirect()
            ->back()
            ->with('success', __('messages.outlet_updated_successfully'));
    }

    public function delete($id)
    {
        $outlet = $this->scopeOutletsForRequest(OutletSetup::query(), request())->findOrFail($id);
        $outlet->delete();

        return redirect()->back()->with('danger', __('messages.outlet_deleted_successfully'));
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
